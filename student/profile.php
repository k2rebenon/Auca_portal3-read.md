<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('student');
require_once __DIR__ . '/../includes/layout.php';
$u = current_user();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contact = trim($_POST['contact']);
    $address = trim($_POST['address']);
    $bio = trim($_POST['bio']);
    $stmt = $conn->prepare("UPDATE users SET contact=?, address=?, bio=? WHERE id=?");
    $stmt->bind_param("sssi", $contact, $address, $bio, $u['id']);
    $stmt->execute();
    $_SESSION['user']['contact'] = $contact;
    $msg = "Profile updated.";
}

$profile = $conn->query("SELECT * FROM users WHERE id={$u['id']}")->fetch_assoc();
$menu = ['dashboard.php'=>'Overview','register_courses.php'=>'Registration','results.php'=>'Results','fees.php'=>'Financial portal','profile.php'=>'My profile'];
render_header('My Profile', $menu, 'profile.php');
?>
<?php if ($msg): ?><div class="success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
<div class="panel" style="max-width:600px">
  <form method="post">
    <div class="field"><label>Full name</label><input value="<?= htmlspecialchars($profile['full_name']) ?>" disabled></div>
    <div class="field"><label>Email</label><input value="<?= htmlspecialchars($profile['email']) ?>" disabled></div>
    <div class="field"><label>Contact number</label><input name="contact" value="<?= htmlspecialchars($profile['contact']) ?>"></div>
    <div class="field"><label>Address</label><input name="address" value="<?= htmlspecialchars($profile['address']) ?>"></div>
    <div class="field"><label>Bio</label><textarea name="bio" rows="3"><?= htmlspecialchars($profile['bio']) ?></textarea></div>
    <button class="btn" type="submit">Save changes</button>
  </form>
</div>
<?php render_footer(); ?>
