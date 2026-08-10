<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('student');
require_once __DIR__ . '/../includes/layout.php';
$u = current_user();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fee_id'])) {
    $fid = (int)$_POST['fee_id'];
    $fee = $conn->query("SELECT * FROM fees WHERE id=$fid AND student_id={$u['id']} AND paid=0")->fetch_assoc();
    if ($fee) {
        $conn->query("INSERT INTO payments (fee_id, student_id, amount, method) VALUES ($fid, {$u['id']}, {$fee['amount']}, 'card')");
        $conn->query("UPDATE fees SET paid=1 WHERE id=$fid");
        $msg = "Payment successful for: " . $fee['description'];
    }
}

$fees = $conn->query("SELECT * FROM fees WHERE student_id={$u['id']} ORDER BY paid, due_date");
$history = $conn->query("SELECT p.*, f.description FROM payments p JOIN fees f ON p.fee_id=f.id WHERE p.student_id={$u['id']} ORDER BY p.paid_at DESC");
$menu = ['dashboard.php'=>'Overview','register_courses.php'=>'Registration','results.php'=>'Results','fees.php'=>'Financial portal','profile.php'=>'My profile'];
render_header('Financial Portal', $menu, 'fees.php');
?>
<?php if ($msg): ?><div class="success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

<div class="panel">
  <h3 style="margin-bottom:12px">Fees</h3>
  <table>
    <tr><th>Description</th><th>Amount</th><th>Due date</th><th>Status</th><th></th></tr>
    <?php while ($f = $fees->fetch_assoc()): ?>
    <tr>
      <td><?= htmlspecialchars($f['description']) ?></td>
      <td>RWF <?= number_format($f['amount']) ?></td>
      <td><?= htmlspecialchars($f['due_date']) ?></td>
      <td><?= $f['paid'] ? '<span class="badge green">Paid</span>' : '<span class="badge red">Unpaid</span>' ?></td>
      <td>
        <?php if (!$f['paid']): ?>
        <form class="inline" method="post">
          <input type="hidden" name="fee_id" value="<?= $f['id'] ?>">
          <button class="btn-sm green">Pay now</button>
        </form>
        <?php endif; ?>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>

<div class="panel">
  <h3 style="margin-bottom:12px">Payment history</h3>
  <table>
    <tr><th>Description</th><th>Amount</th><th>Method</th><th>Date</th></tr>
    <?php while ($p = $history->fetch_assoc()): ?>
    <tr>
      <td><?= htmlspecialchars($p['description']) ?></td>
      <td>RWF <?= number_format($p['amount']) ?></td>
      <td><?= htmlspecialchars($p['method']) ?></td>
      <td><?= htmlspecialchars($p['paid_at']) ?></td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>
<?php render_footer(); ?>
