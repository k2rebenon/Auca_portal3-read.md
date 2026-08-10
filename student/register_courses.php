<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('student');
require_once __DIR__ . '/../includes/layout.php';
$u = current_user();
$msg = '';

// Handle enrollment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['group_id'])) {
    $gid = (int)$_POST['group_id'];
    $exists = $conn->query("SELECT id FROM enrollments WHERE student_id={$u['id']} AND group_id=$gid")->num_rows;
    if (!$exists) {
        $conn->query("INSERT INTO enrollments (student_id, group_id) VALUES ({$u['id']}, $gid)");
        $msg = "Successfully registered for the group.";
    } else {
        $msg = "You are already registered for this group.";
    }
}
// Handle drop
if (isset($_GET['drop'])) {
    $eid = (int)$_GET['drop'];
    $conn->query("DELETE FROM enrollments WHERE id=$eid AND student_id={$u['id']}");
    header("Location: register_courses.php");
    exit;
}

$available = $conn->query("
  SELECT g.id, g.name gname, g.schedule, g.capacity, c.name cname, c.code, t.full_name teacher,
    (SELECT COUNT(*) FROM enrollments e WHERE e.group_id=g.id) taken
  FROM groups_table g JOIN courses c ON g.course_id=c.id LEFT JOIN users t ON g.teacher_id=t.id
  ORDER BY c.code");

$mine = $conn->query("
  SELECT e.id eid, g.name gname, g.schedule, c.name cname, c.code, t.full_name teacher
  FROM enrollments e JOIN groups_table g ON e.group_id=g.id JOIN courses c ON g.course_id=c.id
  LEFT JOIN users t ON g.teacher_id=t.id WHERE e.student_id={$u['id']} AND e.status='active'");

$menu = ['dashboard.php'=>'Overview','register_courses.php'=>'Registration','results.php'=>'Results','fees.php'=>'Financial portal','profile.php'=>'My profile'];
render_header('Course Registration', $menu, 'register_courses.php');
?>
<?php if ($msg): ?><div class="success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

<div class="panel">
  <h3 style="margin-bottom:12px">My registered groups</h3>
  <table>
    <tr><th>Course</th><th>Group</th><th>Teacher</th><th>Schedule</th><th></th></tr>
    <?php while ($r = $mine->fetch_assoc()): ?>
    <tr>
      <td><?= htmlspecialchars($r['code'].' - '.$r['cname']) ?></td>
      <td><?= htmlspecialchars($r['gname']) ?></td>
      <td><?= htmlspecialchars($r['teacher'] ?? '—') ?></td>
      <td><?= htmlspecialchars($r['schedule']) ?></td>
      <td><a class="btn-sm red" href="?drop=<?= $r['eid'] ?>" onclick="return confirm('Drop this group?')">Drop</a></td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>

<div class="panel">
  <h3 style="margin-bottom:12px">Available groups</h3>
  <table>
    <tr><th>Course</th><th>Group</th><th>Teacher</th><th>Schedule</th><th>Seats</th><th></th></tr>
    <?php while ($g = $available->fetch_assoc()): ?>
    <tr>
      <td><?= htmlspecialchars($g['code'].' - '.$g['cname']) ?></td>
      <td><?= htmlspecialchars($g['gname']) ?></td>
      <td><?= htmlspecialchars($g['teacher'] ?? 'TBA') ?></td>
      <td><?= htmlspecialchars($g['schedule']) ?></td>
      <td><?= $g['taken'] ?>/<?= $g['capacity'] ?></td>
      <td>
        <form class="inline" method="post">
          <input type="hidden" name="group_id" value="<?= $g['id'] ?>">
          <button class="btn-sm" <?= $g['taken']>=$g['capacity']?'disabled':'' ?>>Register</button>
        </form>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>
<?php render_footer(); ?>
