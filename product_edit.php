<?php
include 'config.php';
include 'auth.php';
require_role(['owner']);

function h($s){
    return htmlspecialchars($s??'',ENT_QUOTES,'UTF-8');
}

function rupiah($n) { 
    return 'Rp ' . number_format($n, 0, ',', '.'); 
}

$id = (int)($_GET['id'] ?? 0);
$stmt = mysqli_prepare($conn, "SELECT * FROM products WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$p = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if (!$p) die("Produk tidak ditemukan.");

if(isset($_POST['update'])){
    $image = upload_image($_FILES['image']) ?? $p['image'];
    $stmt = mysqli_prepare($conn, "UPDATE products SET name=?, description=?, price=?, image=?, category=?, rating=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "ssissdi", $_POST['name'], $_POST['description'], $_POST['price'], $image, $_POST['category'], $_POST['rating'], $id);
    mysqli_stmt_execute($stmt);
    header("Location: products.php");
    exit;
}

$title="Ubah Produk";
include 'navbar.php';
?>
<form method="POST" enctype="multipart/form-data" class="max-w-md mx-auto mt-10 px-4 space-y-3">
  <h1 class="text-xl font-semibold">Ubah produk</h1>
  <input name="name" value="<?= h($p['name']) ?>" required class="w-full border border-stone-300 rounded px-3 py-2">
  <textarea name="description" rows="3" class="w-full border border-stone-300 rounded px-3 py-2"><?= h($p['description']) ?></textarea>
  <input name="price" type="number" value="<?= $p['price'] ?>" required class="w-full border border-stone-300 rounded px-3 py-2">
  <select name="category" class="w-full border border-stone-300 rounded px-3 py-2">
    <?php foreach (['kopi' => 'Kopi', 'non-kopi' => 'Non Kopi', 'makanan' => 'Makanan'] as $k => $v): ?>
      <option value="<?= $k ?>" <?= $p['category'] === $k ? 'selected' : '' ?>><?= $v ?></option>
    <?php endforeach; ?>
  </select>
<input name="rating" type="number" step="0.1" min="0" max="5" value="<?= $p['rating'] ?>" class="w-full border border-stone-300 rounded px-3 py-2">
  <?php if ($p['image']): ?><img src="uploads/<?= h($p['image']) ?>" class="w-24 h-24 object-cover"><?php endif; ?>
  <input name="image" type="file" accept="image/*" class="text-sm">
  <div class="flex gap-2 justify-end">
    <a href="products.php" class="px-4 py-2 border border-stone-300 rounded">Batal</a>
    <button name="update" class="px-4 py-2 bg-coffee text-white rounded">Simpan perubahan</button>
  </div>
</form>
  </body>
  </html>