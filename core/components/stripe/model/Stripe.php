<?php
require MODX_CORE_PATH . 'components/stripe/vendor/autoload.php';

class Stripe{

    /**
     * @var modX
     */
    public $modx;

    /**
     * @var msOrder
     */
    public $order;

    /**
     * @var $config
     */
    public $config;

    /**
     * @var modUser
     */
    public $user;

    public $customer;

    /**
     * @var modUserProfile
     */
    public $profile;

    public function run(){
        $this->setApiKey();
        return $this->sessionCreate();
    }

    public function __construct(modX $modx, msOrder $order, array $config = [])
    {
        $this->modx = $modx;
        $this->order = $order;
        $this->getUserData();
        $this->setApiKey();
    }

    protected function setApiKey(){
        if(!$key = $this->modx->getOption('stripe_secret_key')){
            $this->modx->log(MODX_LOG_LEVEL_ERROR, 'Unable to initiate Stripe gateway for miniShop28: no secret key is set on the payment method.');
            return false;
        }
        \Stripe\Stripe::setApiKey($this->modx->getOption('stripe_secret_key'));
        return true;
    }

    public function getUserData(){
        /**
         * @var modUser $user
         * @var modUserProfile $profile
         */
        $orderUserId = $this->order->get('user_id');
        $user = $this->modx->getObject('modUser', $orderUserId);
        $profile = $user->Profile;
        $this->user = $user;
        $this->profile = $profile;
    }

    public function getCustomer(){

        if(!$extended = $this->profile->get('extended')){
            $extended = [];
        }

        if(!empty($extended['stripe_customer_id'])){
            $customerId = $extended['stripe_customer_id'];
            try{
                $this->customer = \Stripe\Customer::retrieve($customerId);
            }catch (\Stripe\Error\Base $e){
                $this->modx->log(MODX_LOG_LEVEL_ERROR, '[Stripe] Existing customer error ' . $e->getMessage());
            }
        }

        if(!$this->customer){
            try {
                $this->customer = \Stripe\Customer::create([
                    'email' => $this->profile->get('email'),
                    'description' => '',
                    'metadata' => [
                        'MODX User Id' => $this->user->get('id'),
                        'MODX Username' => $this->user->get('username')
                    ]
                ]);
            }catch (\Stripe\Error\Base $e){
                $this->modx->log(MODX_LOG_LEVEL_ERROR, '[Stripe] Customer Create Error ' . $e->getMessage());
            }
        }

        if(!empty($this->customer)){
            $this->profile->set('extended', [
                'stripe_customer_id' => $this->customer->id
            ]);
            $this->profile->save();
        }

        return true;

    }

    public function PaymentIntent(){
        $amount = $this->getAmountInCents();
        $output = \Stripe\PaymentIntent::create([
            'amount' => $amount,
            'currency' => $this->modx->getOption('stripe_currency'),
            'payment_method_types' => ['card'],
            'receipt_email' => $this->profile->get('email'),
        ]);

    }

    public function sessionCreate(){
        //$this->PaymentIntent();
        $this->getCustomer();
        $amount = $this->getAmountInCents();
        $checkout_session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => $this->modx->getOption('stripe_currency'),
                    'unit_amount' => $amount,
                    'product_data' => [
                        'name' => $this->modx->getOption('site_name'),
                    ],
                ],
                'quantity' => 1,
            ]],
            'metadata' => [
                'order_id' => $this->order->get('num')
            ],
            'customer' => $this->customer ? $this->customer->id : null,
            'mode' => 'payment',
            'success_url' => 'http://stripe.local' . $this->modx->getOption('stripe_success_url') . '?order=' . $this->order->id,
            'cancel_url' => 'http://stripe.local' . $this->modx->getOption('base_url') . $this->modx->getOption('stripe_cancel_url') . '?order=' . $this->order->id,
        ]);

        //Добавить в заказ ID Stripe
        $this->order->set('comment', $checkout_session->id);
        $this->order->save();

        return $checkout_session->id;
    }

    public function verify(){
        if(!$this->setApiKey()){
            $this->modx->log(MODX_LOG_LEVEL_ERROR, 'No Charge ID set yet, source still pending approval.');
            return false;
        }

        try {
            $session = \Stripe\Checkout\Session::retrieve($this->order->get('comment'));
        } catch (\Exception $e) {
            $this->modx->log(MODX_LOG_LEVEL_ERROR, '[Stripe] Verify Fail ' . $e->getMessage());
            return false;
        }

        if($session->payment_status !== 'paid'){
            $this->modx->log(MODX_LOG_LEVEL_ERROR, 'No paid for order id ' . $this->order->get('id'));
            return false;
        }

        $this->order->set('status', 2);
        $this->order->save();
        return true;
    }

    public function getAmountInCents()
    {
        $amount = $this->order->get('cart_cost');
        $amount = (int)($amount * 100);
        return $amount;
    }
}