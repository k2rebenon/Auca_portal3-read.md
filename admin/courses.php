<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');
require_once __DIR__ . '/../includes/layout.php';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] === 'add_course') {
        $code = trim($_POST['code']); $name = trim($_POST['name']); $credits = (int)$_POST['credits'];
        $stmt = $conn->prepare("INSERT INTO courses (code,name,credits) VALUES (?,?,?)");
        $stmt->bind_param("ssi", $code, $name, $credits);
        $stmt->execute();
        $msg = "Course added.";
    }
    if ($_POST['action'] === 'add_group') {
        $course_id = (int)$_POST['course_id']; $name = trim($_POST['name']);
        $teacher_id = $_POST['teacher_id'] ?: null; $schedule = trim($_POST['schedule']); $capacity = (int)$_POST['capacity'];
        $stmt = $conn->prepare("INSERT INTO groups_table (course_id,name,teacher_id,schedule,capacity) VALUES (?,?,?,?,?)");
        $stmt->bind_param("isisi", $course_id, $name, $teacher_id, $schedule, $capacity);
        $stmt->execute();
        $msg = "Group added.";
    }
}
if (isset($_GET['del_course'])) { $conn->query("DELETE FROM courses WHERE id=" . (int)$_GET['del_course']); header("Location: courses.php"); exit; }
if (isset($_GET['del_group'])) { $conn->query("DELETE FROM groups_table WHERE id=" . (int)$_GET['del_group']); header("Location: courses.php"); exit; }

$courses = $conn->query("SELECT * FROM courses ORDER BY code");
$teachers = $conn->query("SELECT id, full_name FROM users WHERE role='teacher'");
$groups = $conn->query("SELECT g.*, c.code, c.name cname, t.full_name teacher FROM groups_table g JOIN courses c ON g.course_id=c.id LEFT JOIN users t ON g.teacher_id=t.id ORDER BY c.code");

$menu = ['dashboard.php'=>'Overview','users.php'=>'Users & roles','courses.php'=>'Courses & groups','fees.php'=>'Fees','reports.php'=>'Reports'];
render_header('Courses & Groups', $menu, 'courses.php');
?>
<?php if ($msg): ?><div class="success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

<div class="panel" style="max-width:600px">
  <h3 style="margin-bottom:12px">Add course</h3>
  <form method="post">
    <input type="hidden" name="action" value="add_course">
    <div class="grid2">
      <div class="field"><label>Code</label><input name="code" required placeholder="CSC 305"></div>
      <div class="field"><label>Name</label><input name="name" required placeholder="Database Systems"></div>
      <div class="field"><label>Credits</label><input type="number" name="credits" value="3" required></div>
    </div>
    <button class="btn" type="submit">Add course</button>
  </form>
</div>

<div class="panel">
  <table>
    <tr><th>Code</th><th>Name</th><th>Credits</th><th></th></tr>
    <?php while ($c = $courses->fetch_assoc()): ?>
    <tr>
      <td><?= htmlspecialchars($c['code']) ?></td><td><?= htmlspecialchars($c['name']) ?></td><td><?= $c['credits'] ?></td>
      <td><a class="btn-sm red" href="?del_course=<?= $c['id'] ?>" onclick="return confirm('Delete course?')">Delete</a></td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>

<div class="panel" style="max-width:600px">
  <h3 style="margin-bottom:12px">Add group (assign teacher)</h3>
  <form method="post">
    <input type="hidden" name="action" value="add_group">
    <div class="field"><label>Course</label>
      <select name="course_id" required>
        <?php $courses->data_seek(0); while ($c = $courses->fetch_assoc()): ?>
        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['code'].' - '.$c['name']) ?></option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="grid2">
      <div class="field"><label>Group name</label><input name="name" required placeholder="Group A"></div>
      <div class="field"><label>Teacher</label>
        <select name="teacher_id">
          <option value="">-- TBA --</option>
          <?php while ($t = $teachers->fetch_assoc()): ?>
          <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['full_name']) ?></option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="field"><label>Schedule</label><input name="schedule" placeholder="Mon/Wed 08:30-10:00"></div>
      <div class="field"><label>Capacity</label><input type="number" name="capacity" value="30"></div>
    </div>
    <button class="btn" type="submit">Add group</button>
  </form>
</div>

<div class="panel">
  <table>
    <tr><th>Course</th><th>Group</th><th>Teacher</th><th>Schedule</th><th></th></tr>
    <?php while ($g = $groups->fetch_assoc()): ?>
    <tr>
      <td><?= htmlspecialchars($g['code'].' - '.$g['cname']) ?></td>
      <td><?= htmlspecialchars($g['name']) ?></td>
      <td><?= htmlspecialchars($g['teacher'] ?? 'TBA') ?></td>
      <td><?= htmlspecialchars($g['schedule']) ?></td>
      <td><a class="btn-sm red" href="?del_group=<?= $g['id'] ?>" onclick="return confirm('Delete group?')">Delete</a></td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>
<?php render_footer(); ?>
