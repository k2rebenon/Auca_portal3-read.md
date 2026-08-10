<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('teacher');
require_once __DIR__ . '/../includes/layout.php';
$u = current_user();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $to = (int)$_POST['receiver_id'];
    $subject = trim($_POST['subject']);
    $body = trim($_POST['body']);
    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, subject, body) VALUES (?,?,?,?)");
    $stmt->bind_param("iiss", $u['id'], $to, $subject, $body);
    $stmt->execute();
    $msg = "Message sent.";
}

$students = $conn->query("SELECT DISTINCT s.id, s.full_name FROM enrollments e JOIN users s ON e.student_id=s.id JOIN groups_table g ON e.group_id=g.id WHERE g.teacher_id={$u['id']} ORDER BY s.full_name");
$sent = $conn->query("SELECT m.*, s.full_name recv FROM messages m JOIN users s ON m.receiver_id=s.id WHERE m.sender_id={$u['id']} ORDER BY m.created_at DESC LIMIT 15");

$menu = ['dashboard.php'=>'Overview','courses.php'=>'My courses','attendance.php'=>'Attendance','grades.php'=>'Grades','messages.php'=>'Messages'];
render_header('Messages', $menu, 'messages.php');
?>
<?php if ($msg): ?><div class="success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

<div class="panel" style="max-width:600px">
  <h3 style="margin-bottom:12px">Send a message</h3>
  <form method="post">
    <div class="field"><label>To</label>
      <select name="receiver_id" required>
        <?php while ($s = $students->fetch_assoc()): ?>
        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['full_name']) ?></option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="field"><label>Subject</label><input name="subject" required></div>
    <div class="field"><label>Message</label><textarea name="body" rows="4" required></textarea></div>
    <button class="btn" type="submit">Send</button>
  </form>
</div>

<div class="panel">
  <h3 style="margin-bottom:12px">Sent messages</h3>
  <table>
    <tr><th>To</th><th>Subject</th><th>Date</th></tr>
    <?php while ($m = $sent->fetch_assoc()): ?>
    <tr><td><?= htmlspecialchars($m['recv']) ?></td><td><?= htmlspecialchars($m['subject']) ?></td><td><?= htmlspecialchars($m['created_at']) ?></td></tr>
    <?php endwhile; ?>
  </table>
</div>
<?php render_footer(); ?>
