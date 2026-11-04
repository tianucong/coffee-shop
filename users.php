<?php

require_once 'config.php';
require_once 'auth.php';
require_role(['owner']);
$msg = "";

function h($s){
    return htmlspecialchars($s ?? '',ENT_QUOTES, 'UTF-8');
}

function rupiah($n){
    return 'Rp '.number_format($n,0,',', '.');
}

if (isset($_POST['add_user'])) {
    $role = $_POST['role'];
    if (!in_array($role, ['cashier', 'owner']) || strlen($_POST['password']) < 6) {
        $msg = "Data tidak valid (password min. 6 karakter).";
    } else {
        $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        try {
            $stmt = mysqli_prepare($conn, "INSERT INTO users (name, username, password, role) VALUES (?,?,?,?)");
            mysqli_stmt_bind_param($stmt, "ssss", $_POST['name'], $_POST['username'], $hash, $role);
            mysqli_stmt_execute($stmt);
            $msg = "Pengguna ditambahkan.";
        } catch (mysqli_sql_exception $e) {
            $msg = "Username sudah dipakai.";
        }
    }
}
$list = mysqli_query($conn, "SELECT id, name, username, role FROM users ORDER BY id");
$title = "Pengguna";
include 'navbar.php';
?>
<div class="max-w-5xl mx-auto px-4 py-8 grid md:grid-cols-3 gap-8">
  <form method="POST" class="space-y-3 border border-stone-200 rounded p-4 h-fit">
    <h2 class="font-semibold">Tambah kasir</h2>
    <?php if ($msg): ?><div class="bg-coffee-soft text-sm p-2 rounded"><?= h($msg) ?></div><?php endif; ?>
    <input name="name" placeholder="Nama" required class="w-full border border-stone-300 rounded px-3 py-2 text-sm">
    <input name="username" placeholder="Username" required class="w-full border border-stone-300 rounded px-3 py-2 text-sm">
    <input name="password" type="password" placeholder="Password" required minlength="6" class="w-full border border-stone-300 rounded px-3 py-2 text-sm">
    <select name="role" class="w-full border border-stone-300 rounded px-3 py-2 text-sm"><option value="cashier">Kasir</option><option value="owner">Owner</option></select>
    <button name="add_user" class="w-full bg-coffee text-white py-2 rounded">Simpan</button>
  </form>
  <table class="md:col-span-2 w-full text-sm h-fit">
    <thead class="bg-coffee-soft text-left"><tr><th class="p-2">Nama</th><th>Username</th><th>Role</th></tr></thead>
    <?php while ($u = mysqli_fetch_assoc($list)): ?>
      <tr class="border-b border-stone-200"><td class="p-2"><?= h($u['name']) ?></td><td><?= h($u['username']) ?></td><td><?= h($u['role']) ?></td></tr>
    <?php endwhile; ?>
  </table>
</div>
<?php ?>
</body>
</html>
