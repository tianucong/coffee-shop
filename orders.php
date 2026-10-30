<?php
include 'config.php';
inlcude 'auth.php';

require_role(['owner']);

function h($s){
    return htmlspecialchars($s ?? '',ENT_QUOTES, 'UTF-8');
}

function rupiah($n){
    return 'Rp '.number_format($n,0,',', '.');
}

