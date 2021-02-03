<?php
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
require_once MODX_CORE_PATH.'model/modx/modx.class.php';
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


require MODX_CORE_PATH . 'components/stripe/vendor/autoload.php';
// Ставим скрытый ключ
\Stripe\Stripe::setApiKey($modx->getOption('stripe_secret_key'));

//pk_test_TYooMQauvdEDq54NiTphI7jx pub
//sk_test_4eC39HqLyjWDarjtT1zdp7dc secret

$order = $modx->getObject('msOrder', (int)$_POST['order']);
//Выбираем мыло пользователя
/**
 * @var modUser $user
 */
$orderUserId = $order->get('user_id');
$user = $modx->getObject('modUser', $orderUserId);
$profile = $user->Profile;
$email = $profile->get('email');


$output = \Stripe\PaymentIntent::create([
    'amount' => $order->get('cart_cost'),
    'currency' => $modx->getOption('stripe_currency'),
    'payment_method_types' => ['card'],
    'receipt_email' => $email,
]);


$checkout_session = \Stripe\Checkout\Session::create([
    'payment_method_types' => ['card'],
    'line_items' => [[
        'price_data' => [
            'currency' => $modx->getOption('stripe_currency'),
            'unit_amount' => $order->get('cart_cost'),
            'product_data' => [
                'name' => $modx->getOption('site_name'),
            ],
        ],
        'quantity' => 1,
    ]],
    'mode' => 'payment',
    'success_url' => 'http://stripe.local'.$modx->getOption('stripe_success_url'),
    'cancel_url' => 'http://stripe.local'.$modx->getOption('stripe_cancel_url'),
]);


echo json_encode(['success' => true, 'message' => $checkout_session->id, 'apiKey' => $modx->getOption('stripe_publishable_key')]);