use strict; use warnings;
#
# Regenerates templates/home8-body.php from templates/home7-body.php.
#
# Home 8 is Home 7's page in a dark theme. Its markup is a copy rather than an
# include, so the two pages stay independent — but that copy goes stale the
# moment Home 7's markup moves. Run this from the repo root after any change to
# Home 7's body and commit the result:
#
#     perl tools/sync-home8.pl
#
# It applies exactly four deltas and fails loudly if any of them no longer
# matches, which is the signal that the change needs looking at by hand rather
# than being copied across blind.
#
sub slurp { my $f=shift; open(my $h,'<:raw',$f) or die "$f: $!"; local $/; my $s=<$h>; close $h; $s }
sub spew  { my ($f,$s)=@_; open(my $h,'>:raw',$f) or die "$f: $!"; print $h $s; close $h }

my $src = 'templates/home7-body.php';
my $dst = 'templates/home8-body.php';
my $s = slurp($src);
my $crlf = ($s =~ /\r\n/) ? 1 : 0;
$s =~ s/\r\n/\n/g;

my $n = 0;
sub rep {
  my ($r,$from,$to,$want) = @_;
  my ($c,$k) = (0,0);
  while (($k = index($$r,$from,$k)) >= 0) { substr($$r,$k,length($from)) = $to; $k += length($to); $c++ }
  die "sync-home8: expected $want of \"" . substr($from,0,54) . "...\", found $c\n" unless $c == $want;
  $n += $c;
}

# 1 · the docblock and the page key
rep(\$s, <<'OLD', <<'NEW', 1);
/**
 * Home 7: exact copy of the standalone redesigned homepage bundle
 * (home (1)/home/), served at /home-7 as a DB-managed page (Admin -> Pages).
 * Its stylesheet is /assets/home7.css and its animation libraries live in
 * /assets/ (matter.min.js, gsap.min.js, ScrollTrigger.min.js), all copied
 * from that bundle. The live "/" homepage is untouched.
 */
$activePage = 'home7';
OLD
/**
 * Home 8: Home 7's page in a dark theme, served at /home-8 as a DB-managed
 * page (Admin -> Pages).
 *
 * GENERATED FILE — do not edit by hand. It is a copy of
 * templates/home7-body.php with four theme deltas applied. After any change to
 * Home 7's body, run `perl tools/sync-home8.pl` from the repo root and commit
 * the result, or the two pages drift apart.
 *
 * Only the theme differs. This page loads /assets/home7.css for the layout,
 * type, spacing and animation, then /assets/home8.css on top of it, which
 * carries nothing but colour. That is deliberate: a layout fix to Home 7
 * reaches Home 8 as well, and the dark theme stays readable as its own file
 * instead of being diffused through a 165KB copy.
 */
$activePage = 'home8';
NEW

# 2 · the theme sheet, after Home 7's
rep(\$s,
  '<link rel="stylesheet" href="<?= asset_url(\'/assets/home7.css\') ?>">',
  '<link rel="stylesheet" href="<?= asset_url(\'/assets/home7.css\') ?>">' . "\n"
  . '<!-- colour only; everything structural comes from home7.css above -->' . "\n"
  . '<link rel="stylesheet" href="<?= asset_url(\'/assets/home8.css\') ?>">',
  1);

# 3 · Home 7 inverts these two buttons inline, because #tech and #why are
#     already dark there. Every section is dark on this page, so they match the
#     rest of the buttons instead.
rep(\$s, 'style="background:#fff;color:#0a1310"', 'style="background:#32b46f;color:#04110a"', 2);

$s =~ s/\n/\r\n/g if $crlf;
spew($dst, $s);
print "sync-home8: $n deltas applied, $dst regenerated (", length($s), " bytes)\n";
