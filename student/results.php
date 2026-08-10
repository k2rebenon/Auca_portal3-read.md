<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('student');
require_once __DIR__ . '/../includes/layout.php';
$u = current_user();

$results = $conn->query("SELECT r.*, c.name cname, c.code FROM results r JOIN courses c ON r.course_id=c.id WHERE r.student_id={$u['id']} ORDER BY c.code");
$menu = ['dashboard.php'=>'Overview','register_courses.php'=>'Registration','results.php'=>'Results','fees.php'=>'Financial portal','profile.php'=>'My profile'];
render_header('My Results', $menu, 'results.php');
?>
<div class="panel">
  <table>
    <tr><th>Course</th><th>Assessment</th><th>Score</th><th>Grade</th></tr>
    <?php while ($r = $results->fetch_assoc()): ?>
    <tr>
      <td><?= htmlspecialchars($r['code'].' - '.$r['cname']) ?></td>
      <td><?= htmlspecialchars($r['assessment']) ?></td>
      <td><?= $r['score'] ?>%</td>
      <td><span class="badge green"><?= htmlspecialchars($r['grade']) ?></span></td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>
<?php render_footer(); ?>
