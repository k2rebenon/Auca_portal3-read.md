<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');
require_once __DIR__ . '/../includes/layout.php';

$by_course = $conn->query("SELECT c.code, c.name, AVG(r.score) avg_score, COUNT(r.id) n
  FROM results r JOIN courses c ON r.course_id=c.id GROUP BY c.id ORDER BY c.code");

$enroll_counts = $conn->query("SELECT c.code, c.name, COUNT(e.id) n
  FROM groups_table g JOIN courses c ON g.course_id=c.id
  LEFT JOIN enrollments e ON e.group_id=g.id GROUP BY c.id ORDER BY c.code");

$fee_summary = $conn->query("SELECT
  (SELECT COALESCE(SUM(amount),0) FROM payments) collected,
  (SELECT COALESCE(SUM(amount),0) FROM fees WHERE paid=0) outstanding")->fetch_assoc();

$menu = ['dashboard.php'=>'Overview','users.php'=>'Users & roles','courses.php'=>'Courses & groups','fees.php'=>'Fees','reports.php'=>'Reports'];
render_header('Reports', $menu, 'reports.php');
?>
<div class="cards">
  <div class="stat"><div class="num">RWF <?= number_format($fee_summary['collected']) ?></div><div class="lbl">Total collected</div></div>
  <div class="stat"><div class="num">RWF <?= number_format($fee_summary['outstanding']) ?></div><div class="lbl">Outstanding</div></div>
</div>

<div class="panel">
  <h3 style="margin-bottom:12px">Average scores by course</h3>
  <table>
    <tr><th>Course</th><th>Avg score</th><th>Records</th></tr>
    <?php while ($r = $by_course->fetch_assoc()): ?>
    <tr><td><?= htmlspecialchars($r['code'].' - '.$r['name']) ?></td><td><?= $r['avg_score'] ? round($r['avg_score'],1).'%' : '—' ?></td><td><?= $r['n'] ?></td></tr>
    <?php endwhile; ?>
  </table>
</div>

<div class="panel">
  <h3 style="margin-bottom:12px">Enrollment by course</h3>
  <table>
    <tr><th>Course</th><th>Students enrolled</th></tr>
    <?php while ($r = $enroll_counts->fetch_assoc()): ?>
    <tr><td><?= htmlspecialchars($r['code'].' - '.$r['name']) ?></td><td><?= $r['n'] ?></td></tr>
    <?php endwhile; ?>
  </table>
</div>
<?php render_footer(); ?>
