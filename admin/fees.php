<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');
require_once __DIR__ . '/../includes/layout.php';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = (int)$_POST['student_id'];
    $desc = trim($_POST['description']);
    $amount = (float)$_POST['amount'];
    $due = $_POST['due_date'];
    $stmt = $conn->prepare("INSERT INTO fees (student_id, description, amount, due_date) VALUES (?,?,?,?)");
    $stmt->bind_param("isds", $student_id, $desc, $amount, $due);
    $stmt->execute();
    $msg = "Fee added.";
}
if (isset($_GET['delete'])) { $conn->query("DELETE FROM fees WHERE id=" . (int)$_GET['delete']); header("Location: fees.php"); exit; }

$students = $conn->query("SELECT id, full_name FROM users WHERE role='student' ORDER BY full_name");
$fees = $conn->query("SELECT f.*, s.full_name FROM fees f JOIN users s ON f.student_id=s.id ORDER BY f.paid, f.due_date");

$menu = ['dashboard.php'=>'Overview','users.php'=>'Users & roles','courses.php'=>'Courses & groups','fees.php'=>'Fees','reports.php'=>'Reports'];
render_header('Fees', $menu, 'fees.php');
?>
<?php if ($msg): ?><div class="success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

<div class="panel" style="max-width:600px">
  <h3 style="margin-bottom:12px">Add fee/invoice for a student</h3>
  <form method="post">
    <div class="field"><label>Student</label>
      <select name="student_id" required>
        <?php while ($s = $students->fetch_assoc()): ?>
        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['full_name']) ?></option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="grid2">
      <div class="field"><label>Description</label><input name="description" required placeholder="Tuition - Semester 2"></div>
      <div class="field"><label>Amount (RWF)</label><input type="number" step="0.01" name="amount" required></div>
      <div class="field"><label>Due date</label><input type="date" name="due_date" required></div>
    </div>
    <button class="btn" type="submit">Add fee</button>
  </form>
</div>

<div class="panel">
  <table>
    <tr><th>Student</th><th>Description</th><th>Amount</th><th>Due date</th><th>Status</th><th></th></tr>
    <?php while ($f = $fees->fetch_assoc()): ?>
    <tr>
      <td><?= htmlspecialchars($f['full_name']) ?></td>
      <td><?= htmlspecialchars($f['description']) ?></td>
      <td>RWF <?= number_format($f['amount']) ?></td>
      <td><?= htmlspecialchars($f['due_date']) ?></td>
      <td><?= $f['paid'] ? '<span class="badge green">Paid</span>' : '<span class="badge red">Unpaid</span>' ?></td>
      <td><a class="btn-sm red" href="?delete=<?= $f['id'] ?>" onclick="return confirm('Delete fee?')">Delete</a></td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>
<?php render_footer(); ?>
