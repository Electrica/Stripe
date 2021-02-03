<?php

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
require_once MODX_CORE_PATH . 'components/stripe/model/Stripe.php';
$modx = new modX();
$modx->initialize('web');
$modx->getService('error', 'error.modError', '', '');
$modx->getService('miniShop2');

$orderId = (int)$_REQUEST['order'];

if(!$orderId){
    return 'Нет ордера';
}

$order = $modx->getObject('msOrder', $orderId);
if(!$order){
    return 'Order object not found';
}

$Stripe = new Stripe($modx, $order);
if($Stripe->verify() === true){
    $modx->sendRedirect($modx->makeUrl(9,'web','','full'));
}