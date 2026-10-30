<?php

include 'config.php';
include 'auth.php';
$error="";

if(isset($_POST['register'])){
    $name=trim($_POST['name']);
    $username=trim($_POST['username']);
    $password=trim($_POST['password']);

    if(strlen($password)<6){
        $error="Password minimal 6 karakter";
    }
    else{
        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        if (mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))) {
            $error = "Username sudah dipakai.";
        } else {
            $hash=password_hash($password,PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, "INSERT INTO users (name, username, password, role) VALUES (?,?,?,'customer')");
            mysqli_stmt_bind_param($stmt, "sss", $name, $username, $hash);
            mysqli_stmt_execute($stmt);
            header("Location: login.php?registered=1");
            exit;
    }
    }
}
$title="Daftar";
include 'navbar.php';
?>
<div class="max-w-sm mx-auto mt-16 px-4">
  <h1 class="text-2xl font-semibold mb-1">Buat akun</h1>
  <p class="text-sm text-stone-500 mb-6">Pelanggan yang login otomatis dapat diskon 10%.</p>
  <?php if ($error): ?><div class="bg-red-50 text-red-700 text-sm p-3 rounded mb-4"><?= h($error) ?></div><?php endif; ?>
  <form method="POST" class="space-y-4">
    <input name="name" placeholder="Nama lengkap" required class="w-full border border-stone-300 rounded px-3 py-2">
    <input name="username" placeholder="Username" required class="w-full border border-stone-300 rounded px-3 py-2">
    <input name="password" type="password" placeholder="Password (min. 6 karakter)" minlength="6" required class="w-full border border-stone-300 rounded px-3 py-2">
    <button name="register" class="w-full bg-coffee text-white py-2 rounded hover:opacity-90">Daftar</button>
  </form>
</div>
</body>
</html>