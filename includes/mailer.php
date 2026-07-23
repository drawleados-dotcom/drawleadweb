<?php
/**
 * Minimal dependency-free SMTP client.
 *
 * There's no Composer/internet access in this hosting setup to pull in
 * PHPMailer, and PHP's built-in mail() has poor deliverability on shared
 * hosting IPs — so this talks raw SMTP over a socket, authenticating
 * against a real mailbox (see SMTP_* constants in includes/config.php),
 * which gives normal inbox deliverability like any other email client.
 *
 * Email sending is best-effort and must never block a booking from being
 * saved — callers should log failures and still tell the user their
 * booking succeeded (the database row is the source of truth).
 */

function smtp_encode_header(string $s): string
{
    if (preg_match('/[^\x20-\x7E]/', $s) && function_exists('mb_encode_mimeheader')) {
        return mb_encode_mimeheader($s, 'UTF-8', 'B', "\r\n");
    }
    return $s;
}

/**
 * Send a single HTML email via SMTP. Returns true on success, false on
 * any failure (connection, auth, or rejection) — check error_log for detail.
 */
function send_email(string $toEmail, string $toName, string $subject, string $htmlBody): bool
{
    if (!defined('SMTP_HOST') || strpos((string) SMTP_USER, 'CHANGE_ME') === 0) {
        error_log('send_email skipped: SMTP is not configured in includes/config.php yet.');
        return false;
    }

    $socket = null;

    try {
        $prefix = SMTP_SECURE === 'ssl' ? 'ssl://' : '';
        $errno = 0;
        $errstr = '';
        $socket = @stream_socket_client($prefix . SMTP_HOST . ':' . SMTP_PORT, $errno, $errstr, 15);
        if (!$socket) {
            error_log("SMTP connect failed: $errstr ($errno)");
            return false;
        }
        stream_set_timeout($socket, 15);

        $readResponse = function () use ($socket): string {
            $data = '';
            while (($line = fgets($socket, 515)) !== false) {
                $data .= $line;
                if (isset($line[3]) && $line[3] === ' ') {
                    break;
                }
            }
            return $data;
        };
        $sendCommand = function (string $cmd) use ($socket): void {
            fwrite($socket, $cmd . "\r\n");
        };
        $expectCode = function (string $response, string $code): bool {
            return (bool) preg_match('/(^|\r\n)' . preg_quote($code, '/') . '/', $response);
        };

        $readResponse(); // server greeting
        $sendCommand('EHLO drawlead.com');
        $readResponse();

        if (SMTP_SECURE === 'tls') {
            $sendCommand('STARTTLS');
            $resp = $readResponse();
            if (!$expectCode($resp, '220') || !@stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                error_log('SMTP STARTTLS failed.');
                fclose($socket);
                return false;
            }
            $sendCommand('EHLO drawlead.com');
            $readResponse();
        }

        $sendCommand('AUTH LOGIN');
        $readResponse();
        $sendCommand(base64_encode(SMTP_USER));
        $readResponse();
        $sendCommand(base64_encode(SMTP_PASS));
        $resp = $readResponse();
        if (!$expectCode($resp, '235')) {
            error_log('SMTP auth failed: ' . trim($resp));
            fclose($socket);
            return false;
        }

        $sendCommand('MAIL FROM:<' . SMTP_FROM_EMAIL . '>');
        $resp = $readResponse();
        if (!$expectCode($resp, '250')) {
            error_log('SMTP MAIL FROM rejected: ' . trim($resp));
            fclose($socket);
            return false;
        }

        $sendCommand('RCPT TO:<' . $toEmail . '>');
        $resp = $readResponse();
        if (!$expectCode($resp, '250') && !$expectCode($resp, '251')) {
            error_log('SMTP RCPT TO rejected for ' . $toEmail . ': ' . trim($resp));
            fclose($socket);
            return false;
        }

        $sendCommand('DATA');
        $resp = $readResponse();
        if (!$expectCode($resp, '354')) {
            error_log('SMTP DATA rejected: ' . trim($resp));
            fclose($socket);
            return false;
        }

        $headers = [
            'From: ' . smtp_encode_header(SMTP_FROM_NAME) . ' <' . SMTP_FROM_EMAIL . '>',
            'To: ' . ($toName !== '' ? smtp_encode_header($toName) . ' <' . $toEmail . '>' : $toEmail),
            'Subject: ' . smtp_encode_header($subject),
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8',
            'Date: ' . date('r'),
            'Message-ID: <' . bin2hex(random_bytes(16)) . '@drawlead.com>',
        ];

        // SMTP data transparency: escape lines that start with a lone ".".
        $body = preg_replace('/\r\n|\r|\n/', "\r\n", $htmlBody);
        $body = preg_replace('/^\./m', '..', $body);

        $message = implode("\r\n", $headers) . "\r\n\r\n" . $body . "\r\n.\r\n";
        fwrite($socket, $message);
        $resp = $readResponse();

        $sendCommand('QUIT');
        fclose($socket);

        if (!$expectCode($resp, '250')) {
            error_log('SMTP message rejected: ' . trim($resp));
            return false;
        }

        return true;
    } catch (\Throwable $e) {
        if (is_resource($socket)) {
            fclose($socket);
        }
        error_log('SMTP send exception: ' . $e->getMessage());
        return false;
    }
}
