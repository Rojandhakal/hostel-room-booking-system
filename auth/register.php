<?php
require '../config/database.php';
$error = "";

if (isset($_POST['register'])) {
    $name  = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $pass  = $_POST['password'];
    $role  = $_POST['role'];

    if ($name && $email && $pass && $role) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email=?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            $error = "Email already exists!";
        } else {
            $hashed = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare(
                "INSERT INTO users (full_name,email,password,role,created_at)
                 VALUES (?,?,?,?,NOW())"
            );
            $stmt->execute([$name, $email, $hashed, $role]);
            header("Location: login.php?signup=success");
            exit;
        }
    } else {
        $error = "All fields are required!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="auth-container">
    <h2>Create Account</h2>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post">
        <input type="text" name="full_name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>

        <select name="role" required>
            <option value="">Select Role</option>
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select>

        <button type="submit" name="register">Register</button>
    </form>

    <p>Already have an account?
        <a href="login.php">Login here</a>
    </p>
</div>

</body>
</html>
