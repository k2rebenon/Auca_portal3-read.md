
<?php
require_once __DIR__ . '/../includes/auth.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $pass  = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, full_name, email, password, role, status FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($pass, $user['password'])) {
        if ($user['status'] === 'disabled') {
            $error = "Your account has been disabled. Contact the administrator.";
        } else {
            unset($user['password']);
            $_SESSION['user'] = $user;
            $dest = ['student' => '../student/dashboard.php',
                     'teacher' => '../teacher/dashboard.php',
                     'admin'   => '../admin/dashboard.php'][$user['role']];
            header("Location: $dest");
            exit;
        }
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html><head><meta charset="UTF-8"><title>Sign in · AUCA Portal</title>
<link rel="stylesheet" href="/auca_portal_3/assets/css/style.css"></head>
<body>
<div class="auth-wrap">
  <div class="auth-side">
    <div>🎓 <b>AUCA STUDENT PORTAL</b></div>
    <h1>Your academic journey, all in one place.</h1>
    <p>Securely access your student, teacher, or admin account.</p>
  </div>
  <div class="auth-form">
    <div class="card-box">
      <div class="tabs">
        <a class="active" href="login.php">Sign in</a>
        <a href="register.php">Create account</a>
      </div>
      <h2>Sign in to your account</h2>
      <p class="sub">Enter your details to continue.</p>
      <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
      <form method="post">
        <div class="field"><label>Email address</label><input type="email" name="email" required></div>
        <div class="field"><label>Password</label><input type="password" name="password" required></div>
        <button class="btn" type="submit">Sign in securely →</button>
      </form>
      <p style="text-align:center;margin-top:14px;font-size:14px">New to AUCA? <a href="register.php" style="color:var(--blue);font-weight:600">Create an account</a></p>
    </div>
  </div>
</div>
</body></html>
