<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('teacher');
require_once __DIR__ . '/../includes/layout.php';
$u = current_user();

$groups = $conn->query("SELECT COUNT(*) c FROM groups_table WHERE teacher_id={$u['id']}")->fetch_assoc()['c'];
$students = $conn->query("SELECT COUNT(DISTINCT e.student_id) c FROM enrollments e JOIN groups_table g ON e.group_id=g.id WHERE g.teacher_id={$u['id']}")->fetch_assoc()['c'];
$mygroups = $conn->query("SELECT g.*, c.name cname, c.code FROM groups_table g JOIN courses c ON g.course_id=c.id WHERE g.teacher_id={$u['id']}");

$menu = ['dashboard.php'=>'Overview','courses.php'=>'My courses','attendance.php'=>'Attendance','grades.php'=>'Grades','messages.php'=>'Messages'];
render_header('Overview', $menu, 'dashboard.php');
?>
<div class="cards">
  <div class="stat"><div class="num"><?= $groups ?></div><div class="lbl">Groups assigned</div></div>
  <div class="stat"><div class="num"><?= $students ?></div><div class="lbl">Total students</div></div>
</div>
<div class="panel">
  <h3 style="margin-bottom:12px">My groups</h3>
  <table>
    <tr><th>Course</th><th>Group</th><th>Schedule</th></tr>
    <?php while ($g = $mygroups->fetch_assoc()): ?>
    <tr><td><?= htmlspecialchars($g['code'].' - '.$g['cname']) ?></td><td><?= htmlspecialchars($g['name']) ?></td><td><?= htmlspecialchars($g['schedule']) ?></td></tr>
    <?php endwhile; ?>
  </table>
</div>
<?php render_footer(); ?>
