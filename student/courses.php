<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('teacher');
require_once __DIR__ . '/../includes/layout.php';
$u = current_user();

$gid = $_GET['g'] ?? null;
$groups = $conn->query("SELECT g.*, c.name cname, c.code FROM groups_table g JOIN courses c ON g.course_id=c.id WHERE g.teacher_id={$u['id']}");

$menu = ['dashboard.php'=>'Overview','courses.php'=>'My courses','attendance.php'=>'Attendance','grades.php'=>'Grades','messages.php'=>'Messages'];
render_header('My Courses', $menu, 'courses.php');
?>
<div class="panel">
  <table>
    <tr><th>Course</th><th>Group</th><th>Schedule</th><th>Roster</th></tr>
    <?php while ($g = $groups->fetch_assoc()): ?>
    <tr>
      <td><?= htmlspecialchars($g['code'].' - '.$g['cname']) ?></td>
      <td><?= htmlspecialchars($g['name']) ?></td>
      <td><?= htmlspecialchars($g['schedule']) ?></td>
      <td><a class="btn-sm" href="?g=<?= $g['id'] ?>">View students</a></td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>

<?php if ($gid): ?>
<div class="panel">
  <h3 style="margin-bottom:12px">Students in group</h3>
  <table>
    <tr><th>Name</th><th>Email</th><th>Contact</th></tr>
    <?php
    $roster = $conn->query("SELECT s.full_name, s.email, s.contact FROM enrollments e JOIN users s ON e.student_id=s.id WHERE e.group_id=" . (int)$gid);
    while ($s = $roster->fetch_assoc()): ?>
    <tr><td><?= htmlspecialchars($s['full_name']) ?></td><td><?= htmlspecialchars($s['email']) ?></td><td><?= htmlspecialchars($s['contact']) ?></td></tr>
    <?php endwhile; ?>
  </table>
</div>
<?php endif; ?>
<?php render_footer(); ?>
