<?php
// Renders the sidebar + topbar shell. Call render_header($title, $menu, $active) then close with render_footer().
function render_header($title, $menu, $active) {
    $user = current_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($title) ?> · AUCA Portal</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="shell">
  <aside class="sidebar">
    <div class="brand">🎓 <span>AUCA</span></div>
    <div class="who"><?= htmlspecialchars($user['full_name']) ?><br><small><?= ucfirst($user['role']) ?></small></div>
    <nav>
      <?php foreach ($menu as $link => $label): ?>
        <a href="<?= $link ?>" class="<?= $active === $link ? 'active' : '' ?>"><?= $label ?></a>
      <?php endforeach; ?>
    </nav>
    <a href="../auth/logout.php" class="logout">Sign out</a>
  </aside>
  <main class="content">
    <h1><?= htmlspecialchars($title) ?></h1>
<?php
}

function render_footer() {
?>
  </main>
</div>
</body>
</html>
<?php
}