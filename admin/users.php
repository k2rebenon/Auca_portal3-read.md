<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');
require_once __DIR__ . '/../includes/layout.php';
$msg = '';

// Create new user (any role - lets admin add teachers/admins)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $pass = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $stmt = $conn->prepare("INSERT INTO users (full_name,email,password,role,contact) VALUES (?,?,?,?,?)");
    $contact = trim($_POST['contact']);
    $stmt->bind_param("sssss", $name, $email, $pass, $role, $contact);
    if ($stmt->execute()) { $msg = "User created."; } else { $msg = "Error: email may already exist."; }
}

// Toggle status
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $conn->query("UPDATE users SET status = IF(status='active','disabled','active') WHERE id=$id");
    header("Location: users.php"); exit;
}
// Change role
if (isset($_GET['set_role']) && isset($_GET['role'])) {
    $id = (int)$_GET['set_role'];
    $role = $conn->real_escape_string($_GET['role']);
    if (in_array($role, ['student','teacher','admin'])) {
        $conn->query("UPDATE users SET role='$role' WHERE id=$id");
    }
    header("Location: users.php"); exit;
}
// Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM users WHERE id=$id");
    header("Location: users.php"); exit;
}

$filter = $_GET['role_filter'] ?? '';
$where = $filter ? "WHERE role='" . $conn->real_escape_string($filter) . "'" : '';
$users = $conn->query("SELECT * FROM users $where ORDER BY role, full_name");

$menu = ['dashboard.php'=>'Overview','users.php'=>'Users & roles','courses.php'=>'Courses & groups','fees.php'=>'Fees','reports.php'=>'Reports'];
render_header('Users & Roles', $menu, 'users.php');
?>
<?php if ($msg): ?><div class="success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

<div class="panel" style="max-width:600px">
  <h3 style="margin-bottom:12px">Add user (student, teacher, or admin)</h3>
  <form method="post">
    <input type="hidden" name="action" value="create">
    <div class="grid2">
      <div class="field"><label>Full name</label><input name="full_name" required></div>
      <div class="field"><label>Email</label><input type="email" name="email" required></div>
      <div class="field"><label>Password</label><input type="password" name="password" required minlength="8"></div>
      <div class="field"><label>Contact</label><input name="contact"></div>
      <div class="field"><label>Role</label>
        <select name="role"><option value="student">Student</option><option value="teacher">Teacher</option><option value="admin">Admin</option></select>
      </div>
    </div>
    <button class="btn" type="submit">Create user</button>
  </form>
</div>

<div class="panel">
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
    <h3>All users</h3>
    <form method="get">
      <select name="role_filter" onchange="this.form.submit()">
        <option value="">All roles</option>
        <option value="student" <?= $filter=='student'?'selected':'' ?>>Students</option>
        <option value="teacher" <?= $filter=='teacher'?'selected':'' ?>>Teachers</option>
        <option value="admin" <?= $filter=='admin'?'selected':'' ?>>Admins</option>
      </select>
    </form>
  </div>
  <table>
    <tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Actions</th></tr>
    <?php while ($u = $users->fetch_assoc()): ?>
    <tr>
      <td><?= htmlspecialchars($u['full_name']) ?></td>
      <td><?= htmlspecialchars($u['email']) ?></td>
      <td>
        <form method="get" class="inline">
          <input type="hidden" name="set_role" value="<?= $u['id'] ?>">
          <select name="role" onchange="this.form.submit()">
            <option value="student" <?= $u['role']=='student'?'selected':'' ?>>Student</option>
            <option value="teacher" <?= $u['role']=='teacher'?'selected':'' ?>>Teacher</option>
            <option value="admin" <?= $u['role']=='admin'?'selected':'' ?>>Admin</option>
          </select>
        </form>
      </td>
      <td><?= $u['status']=='active' ? '<span class="badge green">Active</span>' : '<span class="badge red">Disabled</span>' ?></td>
      <td class="actions">
        <a class="btn-sm" href="?toggle=<?= $u['id'] ?>"><?= $u['status']=='active'?'Disable':'Enable' ?></a>
        <a class="btn-sm red" href="?delete=<?= $u['id'] ?>" onclick="return confirm('Delete this user?')">Delete</a>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>
<?php render_footer(); ?>
