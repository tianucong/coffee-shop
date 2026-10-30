<?php

include 'auth.php';

function h($s){
    return htmlspecialchars($s ?? '',ENT_QUOTES,'UTF-8');
}

function rupiah ($n){
    return 'Rp '.number_formats($n, 0, ',', '.');
}

$role=current_role();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=h($title??'Kopipiro')?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { coffee: { DEFAULT: '#c1bfbe', dark: '#3b2a20', soft: '#f3eee8' } } } } }
    
        function updateCartCount(){
            const cart=JSON.parse(localStorage.getItem('cart')||'{}');
            const total=Object.values(cart).reduce((a+b)=>a+b,0);
            const el=document.getElementById('cartCount');
            if(el){
                el.textContent=total;
            }
        }
        document.addEventListener('DOMContentLoaded',updateCartCount);
        </script>
</head>
<body class="bg-white text-coffee-dark min-h-screen">
<nav class="border-b border-stone-200 bg-white sticky top-0 z-10">
  <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between">
    <a href="index.php" class="text-xl font-bold tracking-wide">KOPIPIRO</a>
    <div class="flex items-center gap-4 text-sm">
      <a href="index.php" class="hover:text-coffee">Menu</a>

      <?php if ($role === 'owner'): ?>
        <a href="orders.php" class="hover:text-coffee">Semua Order</a>
        <a href="products.php" class="hover:text-coffee">Produk</a>
        <a href="users.php" class="hover:text-coffee">Pengguna</a>
      <?php elseif ($role === 'cashier'): ?>
        <a href="cashier.php" class="hover:text-coffee">Order Kasir</a>
      <?php else: ?>
        <a href="checkout.php" class="hover:text-coffee">Keranjang (<span id="cartCount">0</span>)</a>
      <?php endif; ?>

      <?php if (is_logged()): ?>
        <span class="text-stone-500"><?= h($_SESSION['name']) ?> <span class="text-xs bg-coffee-soft px-2 py-0.5 rounded"><?= h($role) ?></span></span>
        <a href="logout.php" class="bg-coffee-dark text-white px-3 py-1.5 rounded">Keluar</a>
      <?php else: ?>
        <a href="login.php" class="hover:text-coffee">Masuk</a>
        <a href="register.php" class="bg-coffee text-white px-3 py-1.5 rounded">Daftar</a>
      <?php endif; ?>
    </div>
  </div>
</nav>
