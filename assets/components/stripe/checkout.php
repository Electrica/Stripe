<?php
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
require_once MODX_CORE_PATH.'model/modx/modx.class.php';
require_once MODX_CORE_PATH . 'components/stripe/model/Stripe.php';
$modx = new modX();
$modx->initialize('web');
$modx->getService('error','error.modError', '', '');

if(!$_POST['order']){
    exit();
}

$miniShop2 = $modx->getService('miniShop2');
if(!$miniShop2){
    echo json_encode(['success' => 'false', 'message' => 'Нет минишопа']);
    exit();
}

$order = $modx->getObject('msOrder', (int)$_POST['order']);
/**
 * TODO Сделать проверку на уже оплаченный.
 * TODO Сделать проверку на ошибки, вывод на экран
 */
$stripe = new Stripe($modx, $order);
$checkout_session = $stripe->run();


echo json_encode(['success' => true, 'message' => $checkout_session, 'apiKey' => $modx->getOption('stripe_publishable_key')]);