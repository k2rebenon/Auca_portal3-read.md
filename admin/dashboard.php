<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');
require_once __DIR__ . '/../includes/layout.php';

$students = $conn->query("SELECT COUNT(*) c FROM users WHERE role='student'")->fetch_assoc()['c'];
$teachers = $conn->query("SELECT COUNT(*) c FROM users WHERE role='teacher'")->fetch_assoc()['c'];
$courses  = $conn->query("SELECT COUNT(*) c FROM courses")->fetch_assoc()['c'];
$revenue  = $conn->query("SELECT COALESCE(SUM(amount),0) t FROM payments")->fetch_assoc()['t'];
$outstanding = $conn->query("SELECT COALESCE(SUM(amount),0) t FROM fees WHERE paid=0")->fetch_assoc()['t'];

$menu = ['dashboard.php'=>'Overview','users.php'=>'Users & roles','courses.php'=>'Courses & groups',
         'fees.php'=>'Fees','reports.php'=>'Reports'];
render_header('Admin Overview', $menu, 'dashboard.php');
?>
<div class="cards">
  <div class="stat"><div class="num"><?= $students ?></div><div class="lbl">Students</div></div>
  <div class="stat"><div class="num"><?= $teachers ?></div><div class="lbl">Teachers</div></div>
  <div class="stat"><div class="num"><?= $courses ?></div><div class="lbl">Courses</div></div>
  <div class="stat"><div class="num">RWF <?= number_format($revenue) ?></div><div class="lbl">Fees collected</div></div>
  <div class="stat"><div class="num">RWF <?= number_format($outstanding) ?></div><div class="lbl">Outstanding fees</div></div>
</div>
<div class="panel">
  <p>Use the sidebar to manage user accounts and roles, configure courses and groups, oversee fees and payments, and view system reports. Admins can also access student and teacher tools directly from the <b>Users</b> page.</p>
</div>
<?php render_footer(); ?>
