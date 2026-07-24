<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_case_studies_access($pdo);

/** Handles one optional image upload for a case study; returns [filename, error]. */
function upload_case_study_image(string $fieldName, string $suffix, string $slug, string $existing): array
{
    if (empty($_FILES[$fieldName]['tmp_name']) || $_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
        return [$existing, ''];
    }
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
    $tmp = $_FILES[$fieldName]['tmp_name'];
    $mime = @mime_content_type($tmp);
    $size = (int) $_FILES[$fieldName]['size'];

    if (!isset($allowed[$mime])) {
        return [$existing, ucfirst($suffix) . ' image must be a JPG, PNG, WEBP, or GIF file.'];
    }
    if ($size > 5 * 1024 * 1024) {
        return [$existing, ucfirst($suffix) . ' image must be smaller than 5MB.'];
    }
    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }
    $ext = $allowed[$mime];
    $base = strtolower(preg_replace('/[^a-z0-9-]+/i', '-', $slug ?: 'case-study'));
    $base = trim($base, '-') ?: 'case-study';
    $filename = $base . '-' . $suffix . '-' . bin2hex(random_bytes(3)) . '.' . $ext;

    if (!move_uploaded_file($tmp, UPLOAD_DIR . $filename)) {
        return [$existing, 'Could not save the uploaded ' . $suffix . ' image. Please try again.'];
    }
    return [$filename, ''];
}

$u = current_user();
$id = (int) ($_GET['id'] ?? 0);
$cs = [
    'id' => 0, 'title' => '', 'slug' => '', 'meta_title' => '', 'meta_description' => '',
    'client_name' => '', 'description' => '', 'problem' => '', 'solution' => '', 'process' => '',
    'result' => '', 'outcome' => '', 'testimonial' => '', 'testimonial_author' => '', 'services' => '',
    'website_link' => '', 'erp_link' => '', 'desktop_image' => '', 'mobile_image' => '', 'result_image' => '',
    'team' => '', 'status' => 'draft',
    'focus_keyword' => '', 'canonical_url' => '', 'robots_index' => 'index', 'robots_follow' => 'follow',
    'og_title' => '', 'og_description' => '', 'og_image' => '',
];

if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM case_studies WHERE id = ?');
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if (!$found) {
        http_response_code(404);
        die('Case study not found.');
    }
    $cs = $found;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $title = trim($_POST['title'] ?? '');
    $rawSlug = trim($_POST['slug'] ?? '');
    $slug = $rawSlug !== '' ? slugify($rawSlug) : slugify($title);
    $metaTitle = trim($_POST['meta_title'] ?? '');
    $metaDescription = trim($_POST['meta_description'] ?? '');
    $clientName = trim($_POST['client_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $problem = trim($_POST['problem'] ?? '');
    $solution = trim($_POST['solution'] ?? '');
    $process = trim($_POST['process'] ?? '');
    $result = trim($_POST['result'] ?? '');
    $outcome = trim($_POST['outcome'] ?? '');
    $testimonial = trim($_POST['testimonial'] ?? '');
    $testimonialAuthor = trim($_POST['testimonial_author'] ?? '');
    $team = trim($_POST['team'] ?? '');
    $websiteLink = trim($_POST['website_link'] ?? '');
    $erpLink = trim($_POST['erp_link'] ?? '');
    $focusKeyword = trim($_POST['focus_keyword'] ?? '');
    $canonicalUrl = trim($_POST['canonical_url'] ?? '');
    $robotsIndex = ($_POST['robots_index'] ?? 'index') === 'noindex' ? 'noindex' : 'index';
    $robotsFollow = ($_POST['robots_follow'] ?? 'follow') === 'nofollow' ? 'nofollow' : 'follow';
    $ogTitle = trim($_POST['og_title'] ?? '');
    $ogDescription = trim($_POST['og_description'] ?? '');
    $ogImage = trim($_POST['og_image'] ?? '');

    $selectedServices = array_intersect(
        array_map('trim', $_POST['services'] ?? []),
        CASE_STUDY_SERVICES
    );
    $services = implode(', ', $selectedServices);

    $statusInput = $_POST['status'] ?? 'draft';
    $status = in_array($statusInput, ['draft', 'published'], true) ? $statusInput : 'draft';

    if ($title === '') {
        $error = 'Title is required.';
    } elseif ($slug === '') {
        $error = 'URL slug is required.';
    } else {
        $dup = $pdo->prepare('SELECT 1 FROM case_studies WHERE slug = ? AND id <> ?');
        $dup->execute([$slug, $id]);
        if ($dup->fetchColumn()) {
            $error = 'That URL is already used by another case study.';
        }
        $dupPage = $pdo->prepare('SELECT 1 FROM pages WHERE slug = ?');
        $dupPage->execute(['/case-studies/' . $slug]);
        if (!$error && $dupPage->fetchColumn()) {
            $error = 'That URL collides with an existing page.';
        }
    }

    $desktopImage = $cs['desktop_image'];
    $mobileImage = $cs['mobile_image'];
    $resultImage = $cs['result_image'];

    if (!$error) {
        [$desktopImage, $err1] = upload_case_study_image('desktop_image', 'desktop', $slug, $desktopImage);
        [$mobileImage, $err2] = upload_case_study_image('mobile_image', 'mobile', $slug, $mobileImage);
        [$resultImage, $err3] = upload_case_study_image('result_image', 'result', $slug, $resultImage);
        $error = $err1 ?: ($err2 ?: $err3);
    }

    if (!$error) {
        if ($id) {
            $pdo->prepare(
                'UPDATE case_studies SET title=?, slug=?, meta_title=?, meta_description=?, client_name=?, description=?,
                 problem=?, solution=?, process=?, result=?, outcome=?, testimonial=?, testimonial_author=?, services=?,
                 website_link=?, erp_link=?, desktop_image=?, mobile_image=?, result_image=?, team=?, status=?,
                 focus_keyword=?, canonical_url=?, robots_index=?, robots_follow=?,
                 og_title=?, og_description=?, og_image=? WHERE id=?'
            )->execute([
                $title, $slug, $metaTitle, $metaDescription, $clientName, $description,
                $problem, $solution, $process, $result, $outcome, $testimonial, $testimonialAuthor, $services,
                $websiteLink, $erpLink, $desktopImage, $mobileImage, $resultImage, $team, $status,
                $focusKeyword, $canonicalUrl, $robotsIndex, $robotsFollow,
                $ogTitle, $ogDescription, $ogImage, $id,
            ]);
        } else {
            $stmt = $pdo->prepare(
                'INSERT INTO case_studies (title, slug, meta_title, meta_description, client_name, description,
                 problem, solution, process, result, outcome, testimonial, testimonial_author, services,
                 website_link, erp_link, desktop_image, mobile_image, result_image, team, status, author_id,
                 focus_keyword, canonical_url, robots_index, robots_follow,
                 og_title, og_description, og_image)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $title, $slug, $metaTitle, $metaDescription, $clientName, $description,
                $problem, $solution, $process, $result, $outcome, $testimonial, $testimonialAuthor, $services,
                $websiteLink, $erpLink, $desktopImage, $mobileImage, $resultImage, $team, $status, $u['id'],
                $focusKeyword, $canonicalUrl, $robotsIndex, $robotsFollow,
                $ogTitle, $ogDescription, $ogImage,
            ]);
            $id = (int) $pdo->lastInsertId();
        }

        $success = $status === 'published' ? 'Published — this case study is now live.' : 'Draft saved.';

        $cs = [
            'id' => $id, 'title' => $title, 'slug' => $slug, 'meta_title' => $metaTitle, 'meta_description' => $metaDescription,
            'client_name' => $clientName, 'description' => $description, 'problem' => $problem, 'solution' => $solution,
            'process' => $process, 'result' => $result, 'outcome' => $outcome, 'testimonial' => $testimonial,
            'testimonial_author' => $testimonialAuthor, 'services' => $services, 'website_link' => $websiteLink,
            'erp_link' => $erpLink, 'desktop_image' => $desktopImage, 'mobile_image' => $mobileImage,
            'result_image' => $resultImage, 'team' => $team, 'status' => $status,
            'focus_keyword' => $focusKeyword, 'canonical_url' => $canonicalUrl,
            'robots_index' => $robotsIndex, 'robots_follow' => $robotsFollow,
            'og_title' => $ogTitle, 'og_description' => $ogDescription, 'og_image' => $ogImage,
        ];
    }
}

$selectedServiceList = $cs['services'] !== '' ? array_map('trim', explode(',', $cs['services'])) : [];

$pageTitle = $id ? 'Edit Case Study' : 'New Case Study';
$pageSub = $cs['title'] ?: 'Untitled';
$activeNav = 'case-studies';
include __DIR__ . '/includes/header.php';
?>

<?php if ($error): ?><div class="alert alert-error"><?= h($error) ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><?= h($success) ?></div><?php endif; ?>

<form method="post" enctype="multipart/form-data">
  <?= csrf_field() ?>

  <div class="card">
    <div class="card-title">Basics</div>
    <div class="field">
      <label for="title">Title</label>
      <input type="text" id="title" name="title" required value="<?= h($cs['title']) ?>" data-slug-source>
    </div>
    <div class="field">
      <label for="slug">URL Slug</label>
      <input type="text" id="slug" name="slug" required value="<?= h($cs['slug']) ?>" data-slug-target>
      <div class="field-hint">Published at yoursite.com/case-studies/<?= h($cs['slug'] ?: 'your-slug') ?></div>
    </div>
    <div class="field">
      <label for="client_name">Client Name</label>
      <input type="text" id="client_name" name="client_name" maxlength="190" value="<?= h($cs['client_name']) ?>" placeholder="Shown on the case study page">
    </div>
    <div class="field">
      <label for="description">Description</label>
      <textarea id="description" name="description" rows="2" maxlength="400"><?= h($cs['description']) ?></textarea>
      <div class="field-hint">Short summary shown on the case studies listing page.</div>
    </div>
  </div>

  <div class="card">
    <div class="card-title">Departments / Services</div>
    <div class="card-desc">Which of your services does this case study belong to?</div>
    <div class="checkbox-grid">
      <?php foreach (CASE_STUDY_SERVICES as $service): ?>
      <label class="checkbox-row">
        <input type="checkbox" name="services[]" value="<?= h($service) ?>" <?= in_array($service, $selectedServiceList, true) ? 'checked' : '' ?>>
        <?= h($service) ?>
      </label>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="card">
    <div class="card-title">The Story</div>
    <div class="field">
      <label for="problem">Problem</label>
      <textarea id="problem" name="problem" rows="4" data-seo-content><?= h($cs['problem']) ?></textarea>
    </div>
    <div class="field">
      <label for="solution">Solution</label>
      <textarea id="solution" name="solution" rows="4" data-seo-content><?= h($cs['solution']) ?></textarea>
    </div>
    <div class="field">
      <label for="process">Process</label>
      <textarea id="process" name="process" rows="4" data-seo-content><?= h($cs['process']) ?></textarea>
    </div>
    <div class="field">
      <label for="result">Result</label>
      <textarea id="result" name="result" rows="4" data-seo-content><?= h($cs['result']) ?></textarea>
    </div>
    <div class="field">
      <label for="outcome">Outcome</label>
      <textarea id="outcome" name="outcome" rows="4" data-seo-content><?= h($cs['outcome']) ?></textarea>
    </div>
  </div>

  <div class="card">
    <div class="card-title">Testimonial</div>
    <div class="field">
      <label for="testimonial">Testimonial</label>
      <textarea id="testimonial" name="testimonial" rows="3"><?= h($cs['testimonial']) ?></textarea>
    </div>
    <div class="field">
      <label for="testimonial_author">Testimonial Author</label>
      <input type="text" id="testimonial_author" name="testimonial_author" maxlength="190" value="<?= h($cs['testimonial_author']) ?>" placeholder="e.g. Rajesh Kumar, Operations Head">
    </div>
  </div>

  <div class="card">
    <div class="card-title">Team</div>
    <div class="field">
      <label for="team">Team Members</label>
      <textarea id="team" name="team" rows="3" placeholder="One per line, e.g.&#10;Vinothkumar Babu — Project Lead&#10;Jane Doe — Developer"><?= h($cs['team']) ?></textarea>
      <div class="field-hint">One team member per line.</div>
    </div>
  </div>

  <div class="card">
    <div class="card-title">Links</div>
    <div class="field">
      <label for="website_link">Website Link</label>
      <input type="url" id="website_link" name="website_link" maxlength="255" value="<?= h($cs['website_link']) ?>" placeholder="https://client-website.com">
    </div>
    <div class="field">
      <label for="erp_link">ERP / Platform Link</label>
      <input type="url" id="erp_link" name="erp_link" maxlength="255" value="<?= h($cs['erp_link']) ?>" placeholder="https://client-erp.com">
    </div>
  </div>

  <div class="card">
    <div class="card-title">Images</div>
    <div class="card-desc">Desktop view, mobile view, and a results screenshot (chart, dashboard, before/after, etc).</div>
    <div class="field">
      <label>Desktop View</label>
      <input type="file" name="desktop_image" accept="image/jpeg,image/png,image/webp,image/gif" data-image-input>
      <div class="upload-preview">
        <img data-image-preview src="<?= h($cs['desktop_image'] ? UPLOAD_URL . $cs['desktop_image'] : '') ?>" style="<?= $cs['desktop_image'] ? '' : 'display:none' ?>">
      </div>
    </div>
    <div class="field">
      <label>Mobile View</label>
      <input type="file" name="mobile_image" accept="image/jpeg,image/png,image/webp,image/gif" data-image-input>
      <div class="upload-preview">
        <img data-image-preview src="<?= h($cs['mobile_image'] ? UPLOAD_URL . $cs['mobile_image'] : '') ?>" style="<?= $cs['mobile_image'] ? '' : 'display:none' ?>">
      </div>
    </div>
    <div class="field">
      <label>Result Image</label>
      <input type="file" name="result_image" accept="image/jpeg,image/png,image/webp,image/gif" data-image-input>
      <div class="upload-preview">
        <img data-image-preview src="<?= h($cs['result_image'] ? UPLOAD_URL . $cs['result_image'] : '') ?>" style="<?= $cs['result_image'] ? '' : 'display:none' ?>">
      </div>
    </div>
  </div>

  <?php
  $seoRow = [
      'focus_keyword' => $cs['focus_keyword'] ?? '',
      'meta_title' => $cs['meta_title'] ?? '',
      'meta_description' => $cs['meta_description'] ?? '',
      'canonical_url' => $cs['canonical_url'] ?? '',
      'robots_index' => $cs['robots_index'] ?? 'index',
      'robots_follow' => $cs['robots_follow'] ?? 'follow',
      'og_title' => $cs['og_title'] ?? '',
      'og_description' => $cs['og_description'] ?? '',
      'og_image' => ($cs['og_image'] ?? '') !== '' ? $cs['og_image'] : ($cs['desktop_image'] ? UPLOAD_URL . $cs['desktop_image'] : ''),
  ];
  $seoPathPrefix = '/case-studies/';
  include __DIR__ . '/includes/seo-panel.php';
  ?>

  <div class="card">
    <div class="field">
      <label for="status">Status</label>
      <select id="status" name="status">
        <option value="draft" <?= $cs['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
        <option value="published" <?= $cs['status'] === 'published' ? 'selected' : '' ?>>Published</option>
      </select>
    </div>
    <button type="submit" class="btn btn-primary">Save Case Study</button>
    <a href="case-studies.php" class="btn btn-ghost">Back to Case Studies</a>
  </div>
</form>

<?php include __DIR__ . '/includes/footer.php'; ?>
