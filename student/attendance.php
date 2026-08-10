<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('teacher');
require_once __DIR__ . '/../includes/layout.php';
$u = current_user();
$msg = '';

$groups = $conn->query("SELECT g.*, c.name cname, c.code FROM groups_table g JOIN courses c ON g.course_id=c.id WHERE g.teacher_id={$u['id']}");
$gid = $_GET['g'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gid = (int)$_POST['group_id'];
    $date = $_POST['att_date'];
    foreach ($_POST['status'] as $student_id => $status) {
        $sid = (int)$student_id;
        $exists = $conn->query("SELECT id FROM attendance WHERE group_id=$gid AND student_id=$sid AND att_date='$date'")->fetch_assoc();
        if ($exists) {
            $conn->query("UPDATE attendance SET status='$status' WHERE id={$exists['id']}");
        } else {
            $conn->query("INSERT INTO attendance (group_id, student_id, att_date, status) VALUES ($gid, $sid, '$date', '$status')");
        }
    }
    $msg = "Attendance saved.";
}

$menu = ['dashboard.php'=>'Overview','courses.php'=>'My courses','attendance.php'=>'Attendance','grades.php'=>'Grades','messages.php'=>'Messages'];
render_header('Attendance', $menu, 'attendance.php');
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

<?php if ($gid): ?>
<div class="panel">
  <form method="post">
    <input type="hidden" name="group_id" value="<?= (int)$gid ?>">
    <div class="field" style="max-width:220px"><label>Date</label><input type="date" name="att_date" value="<?= date('Y-m-d') ?>" required></div>
    <table>
      <tr><th>Student</th><th>Present</th><th>Absent</th><th>Late</th></tr>
      <?php
      $roster = $conn->query("SELECT s.id, s.full_name FROM enrollments e JOIN users s ON e.student_id=s.id WHERE e.group_id=" . (int)$gid);
      while ($s = $roster->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($s['full_name']) ?></td>
        <td><input type="radio" name="status[<?= $s['id'] ?>]" value="present" checked></td>
        <td><input type="radio" name="status[<?= $s['id'] ?>]" value="absent"></td>
        <td><input type="radio" name="status[<?= $s['id'] ?>]" value="late"></td>
      </tr>
      <?php endwhile; ?>
    </table>
    <button class="btn" style="max-width:200px;margin-top:14px" type="submit">Save attendance</button>
  </form>
</div>
<?php endif; ?>
<?php render_footer(); ?>
