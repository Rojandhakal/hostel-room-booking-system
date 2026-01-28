<?php
session_start();
require '../config/database.php';
$error = "";

if(isset($_POST['login'])){
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    if($email && $password && $role){
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email=? AND role=?");
        $stmt->execute([$email, $role]);
        $user = $stmt->fetch();
        if($user && password_verify($password, $user['password'])){
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];
            if($role == 'admin'){
                header("Location: ../admin/dashboard.php");
            } else {
                header("Location: ../client/dashboard.php");
            }
            exit;
        } else {
            $error = "Email or password is incorrect!";
        }
    } else {
        $error = "All fields are required!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<div class="auth-container">
    <h2>Login</h2>
    <?php if($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="post">
        <input type="email" name="email" placeholder="Email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        <input type="password" name="password" placeholder="Password" required>
        <select name="role" required>
            <option value="">Select Role</option>
            <option value="admin" <?= (isset($_POST['role']) && $_POST['role']=='admin')?'selected':'' ?>>Admin</option>
            <option value="user" <?= (isset($_POST['role']) && $_POST['role']=='user')?'selected':'' ?>>User</option>
        </select>
        <button name="login" type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="register.php">Register here</a></p>
</div>
</body>
</html>
