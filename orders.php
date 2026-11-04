<?php

require_once 'config.php';
require_once 'auth.php';
require_role(['owner']);

function h($s){
    return htmlspecialchars($s??'',ENT_QUOTES,'UTF-8');
}

function rupiah($n) { 
    return 'Rp ' . number_format($n, 0, ',', '.'); 
}
$res   = mysqli_query($conn, "SELECT DATE(created_at) AS day, SUM(total) AS sales FROM orders GROUP BY DATE(created_at) ORDER BY day DESC LIMIT 7");
$chart = array_reverse(mysqli_fetch_all($res, MYSQLI_ASSOC)); // dibalik supaya kiri = lama, kanan = baru

$sum = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS n, COALESCE(SUM(total),0) AS revenue FROM orders"));

$orders = mysqli_query($conn, "
  SELECT o.*, c.name AS cashier_name,
         GROUP_CONCAT(CONCAT(p.name, ' x', oi.qty) SEPARATOR ', ') AS items
  FROM orders o
  LEFT JOIN users c ON c.id = o.cashier_id
  JOIN order_items oi ON oi.order_id = o.id
  JOIN products p ON p.id = oi.product_id
  GROUP BY o.id, c.name ORDER BY o.id DESC");

$title = "Semua Order";
include 'navbar.php';
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<div class="max-w-6xl mx-auto px-4 py-8">
  <div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold">Semua Order</h1>
    <a href="export.php" class="bg-coffee text-white px-4 py-2 rounded text-sm">Unduh Excel</a>
  </div>

  <div class="grid md:grid-cols-3 gap-4 mb-8">
    <div class="bg-coffee-soft rounded p-4"><div class="text-sm text-stone-500">Total order</div><div class="text-2xl font-semibold"><?= $sum['n'] ?></div></div>
    <div class="bg-coffee-soft rounded p-4"><div class="text-sm text-stone-500">Total pendapatan</div><div class="text-2xl font-semibold"><?= rupiah($sum['revenue']) ?></div></div>
    <div class="md:col-span-3 border border-stone-200 rounded p-4"><canvas id="salesChart" height="80"></canvas></div>
  </div>

  <div class="overflow-x-auto">
  <table class="w-full text-sm">
    <thead class="bg-coffee-soft text-left"><tr>
      <th class="p-2">#</th><th>Tanggal</th><th>Pemesan</th><th>Item</th><th>Diskon</th><th>Total</th><th>Kasir</th><th>Status</th>
    </tr></thead>
    <tbody>
    <?php while ($o = mysqli_fetch_assoc($orders)): ?>
      <tr class="border-b border-stone-200 align-top">
        <td class="p-2"><?= $o['id'] ?></td>
        <td><?= date('d/m/Y H:i', strtotime($o['created_at'])) ?></td>
        <td><?= h($o['customer_name']) ?> <?= $o['user_id'] ? '' : '<span class="text-xs text-stone-400">(guest)</span>' ?></td>
        <td><?= h($o['items']) ?></td>
        <td><?= rupiah($o['discount']) ?></td>
        <td class="font-medium"><?= rupiah($o['total']) ?></td>
        <td><?= h($o['cashier_name'] ?? '-') ?></td>
        <td><?= h($o['status']) ?></td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
  </div>
</div>
<script>
  const rows = <?= json_encode($chart) ?>;
  new Chart(document.getElementById('salesChart'), {
    type: 'bar',
    data: {
      labels: rows.map(r => r.day),
      datasets: [{ label: 'Penjualan per hari (Rp)', data: rows.map(r => Number(r.sales)), backgroundColor: '#b99b7a' }]
    },
    options: { plugins: { legend: { display: true } } }
  });
</script>
<?php ?>
</body>
</html>
