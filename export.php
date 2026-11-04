<?php

require_once 'config.php';
require_once 'auth.php';
require_role(['owner']);

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Order_Kopipiro.xls");
header("Pragma: no-cache");
header("Expires: 0");

function h($s){
    return htmlspecialchars($s??'',ENT_QUOTES,'UTF-8');
}

function rupiah($n) { 
    return 'Rp ' . number_format($n, 0, ',', '.'); 
}

$result=mysqli_query($conn,"
  SELECT o.*,c.name AS cashier_name
  FROM orders o LEFT JOIN users c ON c.id = o.cashier_id ORDER BY o.id DESC");

?>

<table border="1">
  <tr><th>No Order</th><th>Tanggal</th><th>Pemesan</th><th>Subtotal</th><th>Diskon</th><th>Total</th><th>Kasir</th><th>Status</th></tr>
  <?php while ($o = mysqli_fetch_assoc($result)): ?>
  <tr>
    <td><?= $o['id'] ?></td><td><?= $o['created_at'] ?></td><td><?= h($o['customer_name']) ?></td>
    <td><?= $o['subtotal'] ?></td><td><?= $o['discount'] ?></td><td><?= $o['total'] ?></td>
    <td><?= h($o['cashier_name'] ?? '-') ?></td><td><?= $o['status'] ?></td>
  </tr>
  <?php endwhile; ?>
</table>
