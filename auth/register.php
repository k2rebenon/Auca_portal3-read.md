<?php
require_once __DIR__ . '/../includes/auth.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $pass = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    $contact = trim($_POST['contact']);
    $address = trim($_POST['address']);
    $dob = $_POST['dob'];
    $occupation = trim($_POST['occupation']);
    $civil = $_POST['civil_status'];
    $gender = $_POST['gender'];
    $religion = trim($_POST['religion']);
    $bio = trim($_POST['bio']);

    if (strlen($pass) < 8) {
        $error = "Password must be at least 8 characters.";
    } elseif ($pass !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE email=?");
        $check->bind_param("s", $email);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $error = "That email is already registered.";
        } else {
            $hash = password_hash($pass, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("INSERT INTO users (full_name,email,password,role,contact,address,dob,occupation,civil_status,gender,religion,bio)
                                     VALUES (?,?,?,'student',?,?,?,?,?,?,?,?)");
            $stmt->bind_param("sssssssssss",$name,$email,$hash,$contact,$address,$dob,$occupation,$civil,$gender,$religion,$bio);
            $stmt->execute();
            header("Location: login.php?registered=1");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html><head><meta charset="UTF-8"><title>Create account · AUCA Portal</title>
<link rel="stylesheet" href="/auca_portal_3/assets/css/style.css"></head>
<body>
<div class="auth-wrap">
  <div class="auth-side">
    <div>🎓 <b>AUCA STUDENT PORTAL</b></div>
    <h1>Your academic journey, all in one place.</h1>
    <p>Complete your student KYC profile to get started.</p>
  </div>
  <div class="auth-form">
    <div class="card-box" style="max-width:560px">
      <div class="tabs">
        <a href="login.php">Sign in</a>
        <a class="active" href="register.php">Create account</a>
      </div>
      <h2>Create your account</h2>
      <p class="sub">All fields are required except bio.</p>
      <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
      <form method="post">
        <div class="grid2">
          <div class="field"><label>Full name</label><input name="full_name" required></div>
          <div class="field"><label>Email address</label><input type="email" name="email" required></div>
          <div class="field"><label>Password</label><input type="password" name="password" required minlength="8"></div>
          <div class="field"><label>Contact number</label><input name="contact" required></div>
          <div class="field"><label>Residential address</label><input name="address" required></div>
          <div class="field"><label>Date of birth</label><input type="date" name="dob" required></div>
          <div class="field"><label>Occupation</label><input name="occupation" value="Student" required></div>
          <div class="field"><label>Civil status</label>
            <select name="civil_status" required><option>Single</option><option>Married</option><option>Other</option></select>
          </div>
          <div class="field"><label>Gender</label>
            <select name="gender" required><option>Female</option><option>Male</option><option>Other</option></select>
          </div>
          <div class="field"><label>Religion</label><input name="religion" placeholder="Prefer not to say"></div>
        </div>
        <div class="field"><label>Bio (optional)</label><textarea name="bio" rows="3"></textarea></div>
        <div class="field"><label>Confirm password</label><input type="password" name="confirm_password" required></div>
        <button class="btn" type="submit">Complete registration →</button>
      </form>
      <p style="text-align:center;margin-top:14px;font-size:14px">Already have an account? <a href="login.php" style="color:var(--blue);font-weight:600">Sign in</a></p>
    </div>
  </div>
</div>
</body></html>
