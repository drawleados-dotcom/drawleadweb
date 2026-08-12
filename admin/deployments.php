<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_admin();

// This repo is private, so GitHub's API needs a token to read deploy
// history from here directly — nothing this admin panel has today.
// Until that's wired up, this page is a fast, correct way in: direct
// links to GitHub's own Actions run history (every push to main runs
// the deploy workflow, so that list IS the deployment history) and
// repo, plus the facts about how the deploy actually works, read
// straight from .github/workflows/deploy.yml rather than hardcoded.
$repoSlug = 'drawleados-dotcom/drawleadweb';
$repoUrl = 'https://github.com/' . $repoSlug;
$workflowRunsUrl = $repoUrl . '/actions/workflows/deploy.yml';
$actionsUrl = $repoUrl . '/actions';

$pageTitle = 'Production Environment';
$pageSub = 'Deployment history for admin.drawlead.com.';
$activeNav = 'deployments';
include __DIR__ . '/includes/header.php';
?>

<div class="card">
  <div class="card-title">How deploys work</div>
  <div class="card-desc">Every push to the <code>main</code> branch runs a GitHub Actions workflow that syncs the repository straight to admin.drawlead.com over FTP/SFTP — no manual upload step.</div>
  <table>
    <tbody>
      <tr><td style="width:180px;color:var(--g500)">Repository</td><td><a href="<?= h($repoUrl) ?>" target="_blank" rel="noopener">github.com/<?= h($repoSlug) ?> ↗</a></td></tr>
      <tr><td style="color:var(--g500)">Deploy target</td><td>admin.drawlead.com</td></tr>
      <tr><td style="color:var(--g500)">Trigger</td><td>Every push to <code>main</code>, or run manually from the Actions tab</td></tr>
      <tr><td style="color:var(--g500)">Method</td><td>FTP/SFTP file sync (FTP-Deploy-Action)</td></tr>
      <tr><td style="color:var(--g500)">Workflow file</td><td><code>.github/workflows/deploy.yml</code></td></tr>
    </tbody>
  </table>
</div>

<div class="card">
  <div class="card-title">All Deployments</div>
  <div class="card-desc">Every run of the deploy workflow — status, commit, and how long ago — lives on GitHub. This panel doesn't have a token to pull that list in directly yet, so the fastest accurate view is GitHub's own page.</div>
  <a href="<?= h($workflowRunsUrl) ?>" target="_blank" rel="noopener" class="btn btn-primary">View All Deployments on GitHub ↗</a>
  <a href="<?= h($actionsUrl) ?>" target="_blank" rel="noopener" class="btn btn-ghost">View All Workflow Runs ↗</a>
</div>

<div class="access-note">Want live deploy status shown directly on this page instead of linking out? That needs a GitHub personal access token (repo → Settings → Developer settings → Personal access tokens, "Actions: Read-only" scope) added to <code>includes/config.php</code> on the server — ask and it can be wired up next.</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
