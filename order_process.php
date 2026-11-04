<?php

require_once 'config.php';
require_once 'auth.php';

header('Content-Type:application/json');

$data=json_decode(file_get_contents('php://input'),true);
$items=$data['items']??[];
$name=is_logged()?$_SESSION['name']:trim($data['customer_name']??'');

if(!$items||$name===''){
    echo json_encode(['ok'=>false,'msg'=>'Data pesanan tidak lengkap']);
    exit;
}

$subtotal=0;
$rows=[];
$stmt=mysqli_prepare($conn,"SELECT price FROM products WHERE id=?");
foreach($items as $it){
    $pid=(int)$it['id'];
    $qty=max(1,(int)$it['qty']);
    mysqli_stmt_bind_param($stmt,"i",$pid);
    mysqli_stmt_execute($stmt);
    $p=mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if(!$p){
        continue;
    }
    $subtotal+=$p['price']*$qty;
    $rows[]=['pid'=>$pid,'qty'=>$qty,'price'=>$p['price']];
}
if(!$rows){
    echo json_encode(['ok'=>false,'msg'=>'Produk tidak valid']);
    exit;
}

$discount=(int)round($subtotal*get_discount_percent()/100);
$total=$subtotal-$discount;
$userId=is_logged()?$_SESSION['user_id']:null;

mysqli_begin_transaction($conn);
try{
    $s=mysqli_prepare($conn,"INSERT INTO orders (user_id,customer_name, subtotal,discount,total) VALUES (?,?,?,?,?)");
    mysqli_stmt_bind_param($s,"isiii",$userId,$name,$subtotal,$discount,$total);
    mysqli_stmt_execute($s);
    $orderId=mysqli_insert_id($conn);

    $s=mysqli_prepare($conn,"INSERT INTO order_items (order_id,product_id,qty,price) VALUES (?,?,?,?)");
    foreach($rows as $r){
        mysqli_stmt_bind_param($s,"iiii",$orderId,$r['pid'],$r['qty'],$r['price']);
        mysqli_stmt_execute($s);
    }
    mysqli_commit($conn);
    echo json_encode(['ok'=>true,'order_id'=>$orderId]);
}
catch(Exception $e){
    mysqli_rollback($conn);
    echo json_encode(['ok'=>false,'msg'=>'Gagal menyimpan pesanan']);
}