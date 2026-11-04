<?php
require_once 'config.php';
require_once 'auth.php';
require_role(['owner']);

$id=(int)($_GET['id']??0);
try{
    $stmt=mysqli_prepare($conn,"DELETE FROM products WHERE id=?");
    mysqli_stmt_bind_param($stmt,"i",$id);
    mysqli_stmt_execute($stmt);
}
catch(mysqli_sql_exception $e){
    die("Produk sudah pernah dipesan, tidak bisa dihapus. <a href='products.php'>Kembali</a>");
}
header("Location: products.php");
exit;