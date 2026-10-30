<?php

include 'config.php';
include 'auth.php';

require_role (['cashier']);

function h($s){
    return htmlspecialchar($s ?? '',ENT_QUOTES,'UTF-8');
}

function rupiah($n){
    return 'Rp ' . number_format($n, 0, ',', '.'); 
}

$me=$_SESSION['user_id'];

$sql="SELECT o.*, GROUP_CONCAT(CONCAT(p.name,' x',oi.qty) SEPARATOR ', ')AS items
FROM orders o
JOIN order_items oi ON oi.order_id = o.id
JOIN products p ON p.id = oi.product_id
WHERE %s GROUP BY o.id ORDER BY o.id DESC";

$newOrders = mysqli_fetch_all(mysqli_query($conn, sprintf($sql, "o.cashier_id IS NULL")), MYSQLI_ASSOC);
$myOrders = mysqli_fetch_all(mysqli_query($conn, sprintf($sql, "o.cashier_id = " . (int)$me)), MYSQLI_ASSOC);

$title="Order Kasir";
include "navbar.php";

function order_card($o,$button){?>
    <div class="border border-stone-200 rounded p-4 flex justify-between gap-4 items-center">
    <div class="text-sm">
        <div class="font-medium">#<?=$o['id']?> . <?=h($o['customer_name'])?>.<?=rupiah=h($o['total'])?></div>
        <div class="text-stone-500"><?= h($o['items']) ?></div>
        <div class="text-xs text-stone-400"><?= date('d/m/Y H:i', strtotime($o['created_at'])) ?> · <?= h($o['status']) ?></div>
</div>
<?=$button?>
</div>
<?php
}
?>

<div class="max-w-3xl mx-auto px-4 py-8 space-y-8">

<section>
    <h2 class="font-semibold mb-3">Order baru (<?=count($newOrder)?>)</h2>
    <div class="space-y-3">
    <?php
    foreach($newOrders as $o):
        $tombolAmbil='<form method="POST" action="order_update.php">
                        <input type="hidden" name="id" value="'.$o['id'].'">
                          <button name="action" value="claim" class="bg-coffee text-white px-4 py-2 rounded text-sm">Ambil</button>
                        </form>';
        order_card($o,$tombolAmbil);
    endforeach;?>
    <?php if (!$newOrders): ?>
        <p class="text-stone-500 text-sm">Belum ada order baru.</p>
    <?php endif; ?>
    </div>
    </section>

<section>
    <h2 class="font-semibold mb-3">Order saya (<?= count($myOrders) ?>)</h2>
    <div class="space-y-3">
        <?php
        foreach($myOrders as $o):
        
            $btn=$o['status']==='done' ? '<span class="text-green-700 text-sm">Selesai</span>'
            : '<form method="POST" action="order_update.php">
               <input type="hidden" name="id" value="'.$o['id'].'">
               <button name="action" value="done" class="bg-coffee-dark text-white px-4 py-2 rounded text-sm">Tandai selesai</button>
             </form>';
             order_card($o,$btn);
        endforeach;?>

        <?php if (!$myOrders): ?>
        <p class="text-stone-500 text-sm">Kamu belum mengambil order.</p>
      <?php endif; ?>
    </div>
    </section>
    </div>

</body>
</html>