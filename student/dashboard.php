<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('student');
require_once __DIR__ . '/../includes/layout.php';
$u = current_user();

$gpa_row = $conn->query("SELECT AVG(score) avg_score FROM results WHERE student_id={$u['id']}")->fetch_assoc();
$gpa = $gpa_row['avg_score'] ? round($gpa_row['avg_score']/25, 2) : 0; // rough 0-4 scale from %
$courses = $conn->query("SELECT COUNT(*) c FROM enrollments WHERE student_id={$u['id']} AND status='active'")->fetch_assoc()['c'];
$results = $conn->query("SELECT r.*, c.name course_name FROM results r JOIN courses c ON r.course_id=c.id WHERE r.student_id={$u['id']} ORDER BY r.id DESC LIMIT 5");
$fees_due = $conn->query("SELECT COALESCE(SUM(amount),0) t FROM fees WHERE student_id={$u['id']} AND paid=0")->fetch_assoc()['t'];

$menu = [
  'dashboard.php' => 'Overview', 'register_courses.php' => 'Registration', 'results.php' => 'Results',
  'fees.php' => 'Financial portal', 'profile.php' => 'My profile',
];
render_header('Overview', $menu, 'dashboard.php');
?>
<p>Here is what's happening with your studies today.</p>
<div class="cards" style="margin-top:20px">
  <div class="stat"><div class="num"><?= $gpa ?></div><div class="lbl">Current GPA (approx)</div></div>
  <div class="stat"><div class="num"><?= $courses ?></div><div class="lbl">Enrolled courses</div></div>
  <div class="stat"><div class="num">RWF <?= number_format($fees_due) ?></div><div class="lbl">Outstanding fees</div></div>
  <div class="stat"><div class="num"><?= $u['status']=='active'?'Active':'Disabled' ?></div><div class="lbl">Account status</div></div>
</div>

<div class="panel">
  <h3 style="margin-bottom:12px">Recent results</h3>
  <table>
    <tr><th>Course</th><th>Assessment</th><th>Score</th><th>Grade</th></tr>
    <?php while ($r = $results->fetch_assoc()): ?>
    <tr>
      <td><?= htmlspecialchars($r['course_name']) ?></td>
      <td><?= htmlspecialchars($r['assessment']) ?></td>
      <td><?= $r['score'] ?>%</td>
      <td><span class="badge green"><?= htmlspecialchars($r['grade']) ?></span></td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>
<?php render_footer(); ?>
