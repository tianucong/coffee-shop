<?php
require_once 'config.php';
require_once 'auth.php';

function h($s){
    return htmlspecialchars($s??'',ENT_QUOTES,'UTF-8');
}

function rupiah($n) { 
    return 'Rp ' . number_format($n, 0, ',', '.'); 
}

$id = (int)($_GET['id'] ?? 0);
$stmt = mysqli_prepare($conn, "SELECT * FROM orders WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$order = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if (!$order) die("Order tidak ditemukan.");

$stmt = mysqli_prepare($conn, "SELECT p.name, oi.qty, oi.price FROM order_items oi JOIN products p ON p.id = oi.product_id WHERE oi.order_id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$items = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

$title = "Pesanan diterima";
include 'navbar.php';
?>
<div class="max-w-md mx-auto mt-12 px-4">
  <div class="text-center mb-6">
    <div class="text-5xl">✅</div>
    <h1 class="text-2xl font-semibold mt-2">Pesanan diterima</h1>
    <p class="text-stone-500 text-sm">Nomor order #<?= $order['id'] ?> atas nama <?= h($order['customer_name']) ?></p>
  </div>
  <div class="border border-stone-200 rounded p-4 text-sm space-y-2">
    <?php foreach ($items as $i): ?>
      <div class="flex justify-between"><span><?= h($i['name']) ?> × <?= $i['qty'] ?></span><span><?= rupiah($i['price'] * $i['qty']) ?></span></div>
    <?php endforeach; ?>
    <div class="flex justify-between border-t pt-2"><span>Subtotal</span><span><?= rupiah($order['subtotal']) ?></span></div>
    <div class="flex justify-between text-green-700"><span>Diskon</span><span>− <?= rupiah($order['discount']) ?></span></div>
    <div class="flex justify-between font-semibold text-base"><span>Total</span><span><?= rupiah($order['total']) ?></span></div>
  </div>
  <a href="index.php" class="block text-center mt-6 border border-coffee-dark py-2 hover:bg-coffee-dark hover:text-white">Kembali ke menu</a>
</div>
<?php ?>
</body>
</html>
