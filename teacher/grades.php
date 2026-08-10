<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('teacher');
require_once __DIR__ . '/../includes/layout.php';
$u = current_user();
$msg = '';

$groups = $conn->query("SELECT g.*, c.id course_id, c.name cname, c.code FROM groups_table g JOIN courses c ON g.course_id=c.id WHERE g.teacher_id={$u['id']}");
$gid = $_GET['g'] ?? null;

function grade_from_score($s) {
    if ($s >= 90) return 'A'; if ($s >= 80) return 'A-'; if ($s >= 70) return 'B+';
    if ($s >= 60) return 'B'; if ($s >= 50) return 'C'; return 'F';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gid = (int)$_POST['group_id'];
    $course_id = (int)$_POST['course_id'];
    $assessment = trim($_POST['assessment']);
    foreach ($_POST['score'] as $student_id => $score) {
        if ($score === '') continue;
        $sid = (int)$student_id;
        $score = (float)$score;
        $grade = grade_from_score($score);
        $conn->query("INSERT INTO results (student_id, course_id, assessment, score, grade) VALUES ($sid, $course_id, '$assessment', $score, '$grade')");
    }
    $msg = "Grades recorded for: $assessment";
}

$menu = ['dashboard.php'=>'Overview','courses.php'=>'My courses','attendance.php'=>'Attendance','grades.php'=>'Grades','messages.php'=>'Messages'];
render_header('Grades', $menu, 'grades.php');
?>
<?php if ($msg): ?><div class="success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

<div class="panel">
  <form method="get">
    <div class="field"><label>Select group</label>
      <select name="g" onchange="this.form.submit()">
        <option value="">-- choose --</option>
        <?php $groups->data_seek(0); while ($g = $groups->fetch_assoc()): ?>
        <option value="<?= $g['id'] ?>" <?= $gid==$g['id']?'selected':'' ?>><?= htmlspecialchars($g['code'].' - '.$g['name']) ?></option>
        <?php endwhile; ?>
      </select>
    </div>
  </form>
</div>

<?php if ($gid):
  $groups->data_seek(0);
  $current = null;
  while ($g = $groups->fetch_assoc()) if ($g['id'] == $gid) $current = $g;
?>
<div class="panel">
  <form method="post">
    <input type="hidden" name="group_id" value="<?= (int)$gid ?>">
    <input type="hidden" name="course_id" value="<?= (int)$current['course_id'] ?>">
    <div class="field" style="max-width:300px"><label>Assessment name</label><input name="assessment" placeholder="e.g. Midterm, Quiz 1" required></div>
    <table>
      <tr><th>Student</th><th>Score (%)</th></tr>
      <?php
      $roster = $conn->query("SELECT s.id, s.full_name FROM enrollments e JOIN users s ON e.student_id=s.id WHERE e.group_id=" . (int)$gid);
      while ($s = $roster->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($s['full_name']) ?></td>
        <td><input type="number" step="0.01" min="0" max="100" name="score[<?= $s['id'] ?>]" style="width:100px"></td>
      </tr>
      <?php endwhile; ?>
    </table>
    <button class="btn" style="max-width:200px;margin-top:14px" type="submit">Save grades</button>
  </form>
</div>
<?php endif; ?>
<?php render_footer(); ?>
