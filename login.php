<?php

include 'config.php';
include 'auth.php';
$error="";


if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = mysqli_prepare($conn, "SELECT id, name, username, password, role FROM users WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id']=$user['id'];
        $_SESSION['name']=$user['name'];
        $_SESSION['role']=$user['role'];

    if($user['role']==='owner'){
        header("Location: orders.php");
    }
    elseif($user['role']==='cashier'){
        header("Location: cashier.php");
    }
    else{
        header("Location: index.php");
    }
    exit;
}
$error="Username atau password salah";
}
$title="Masuk";
include 'header.php';
?>
<div class="max-w-sm mx-auto mt-16 px-4">
    <h1 class="text-2xl font-semibold mb-6">Masuk</h1>
    <?php if(isset($_GET['registered'])):?><div class="bg-green-50 text-green-700 text-sm p-3 rounded mb-4">Akun dibuat. Silakan masuk.</div><?php endif; ?>
    <?php if($error):?><div class="bg-red-50 text-red-700 text-sm p-3 rounded mb-4"><?= h($error) ?></div><?php endif; ?>
    <form method="POST" class="space-y-4">
    <input name="username" placeholder="Username" required autofocus class="w-full border border-stone-300 rounded px-3 py-2">
    <input name="password" type="password" placeholder="Password" required class="w-full border border-stone-300 rounded px-3 py-2">
    <button name="login" class="w-full bg-coffee-dark text-white py-2 rounded hover:opacity-90">Masuk</button>
  </form>
  <p class="text-sm text-stone-500 mt-4">Belum punya akun? <a href="register.php" class="text-coffee underline">Daftar</a></p>
</div>
</body>
</html>
