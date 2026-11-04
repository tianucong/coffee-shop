<?php
require_once 'config.php';
require_once 'auth.php';
require_role(['owner']);

$msg="";

function h($s){
    return htmlspecialchars($s??'',ENT_QUOTES,'UTF-8');
}

function rupiah($n) { 
    return 'Rp ' . number_format($n, 0, ',', '.'); 
}

if(isset($_POST['add'])){
    $image_url=trim($_POST['image_url']);
    $stmt=mysqli_prepare($conn,"INSERT INTO products (name,description,price,image,category,rating) VALUES (?,?,?,?,?,?)");
    mysqli_stmt_bind_param($stmt, "ssissd", $_POST['name'], $_POST['description'], $_POST['price'], $image_url, $_POST['category'], $_POST['rating']);
    if(mysqli_stmt_execute($stmt)){
        $msg="Produk berhasil ditambahkan";
    }
    else{
        $msg="Gagal menambah produk";
    }

}

$list=mysqli_query($conn,"SELECT * FROM products ORDER BY id DESC");

$title="Kelola Produk";
include 'navbar.php';
?>

<div class="max-w-6xl mx-auto px-4 py-8 grid md:grid-cols-3 gap-8">
    <form method="POST" class="space-y-3 border border-stone-200 rounded p-4 h-fit">
    <h2 class="font-semibold">Tambah produk</h2>
    <?php if($msg): ?>
        <div class="bg-green-50 text-green-700 text-sm p-2 rounded"><?= h($msg) ?></div>
    <?php endif; ?>
    <input name="name" placeholder="Nama produk" required class="w-full border border-stone-300 rounded px-3 py-2 text-sm">
    <textarea name="description" placeholder="Deskripsi" rows="3" class="w-full border border-stone-300 rounded px-3 py-2 text-sm"></textarea>
    <input name="price" type="number" min="0" placeholder="Harga (Rp)" required class="w-full border border-stone-300 rounded px-3 py-2 text-sm">

    <select name="category" class="w-full border border-stone-300 rounded px-3 py-2 text-sm">
      <option value="kopi">Kopi</option>
      <option value="non-kopi">Non Kopi</option>
      <option value="makanan">Makanan</option>
    </select>
    <input name="rating" type="number" step="0.1" min="0" max="5" value="4.5" class="w-full border border-stone-300 rounded px-3 py-2 text-sm">
    <input name="image_url" type="url" placeholder="Link URL Gambar (opsional)" class="w-full border border-stone-300 rounded px-3 py-2 text-sm">
    <button name="add" class="w-full bg-coffee text-white py-2 rounded">Simpan produk</button>
  </form>

<div class="md:col-span-2">
    <table class="w-full text-sm">
      <thead class="bg-coffee-soft text-left">
        <tr>
          <th class="p-2">Gambar</th>
          <th>Nama</th>
          <th>Kategori</th>
          <th>Harga</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php while($p=mysqli_fetch_assoc($list)):?>
            <tr class="border-b border-stone-200">
                <td class="p-2">
                    <?php
                    if (!empty($p['image'])) {
                        echo '<img src="'.h($p['image']).'" class="w-12 h-12 object-cover rounded shadow-sm">';
                    } 
                    else {
                        echo '<span class="text-2xl">☕</span>';
                    }
                    ?>
                    </td>
                    <td class="font-medium"><?= h($p['name']) ?></td>
                    <td><span class="bg-stone-100 px-2 py-1 rounded text-xs"><?= h(ucfirst($p['category'])) ?></span></td>
                    <td><?= rupiah($p['price']) ?></td>
                    <td class="text-right space-x-2">
                        <a href="product_edit.php?id=<?= $p['id'] ?>" class="text-coffee underline hover:text-coffee-dark">Ubah</a>
                        <a href="product_delete.php?id=<?= $p['id'] ?>" onclick="return confirm('Yakin ingin menghapus produk ini?')" class="text-red-600 underline hover:text-red-800">Hapus</a>
                    </td>
        </tr>
        <?php endwhile;?>
</tbody>
    </table>
  </div>
</div>

        </body>
        </html>
