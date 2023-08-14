<?php
/**
 * https://stripe.com/docs/development/quickstart
 */
if (!class_exists('msPaymentInterface')) {
    /** @noinspection PhpIncludeInspection */
    require_once MODX_CORE_PATH . 'components/minishop2/handlers/mspaymenthandler.class.php';
}


class StripePayment extends msPaymentHandler implements msPaymentInterface
{
    /** @var modX $modx */
    public $modx;
    public $order;

    function __construct(modX $modx, array $config = [])
    {
        parent::__construct($modx, $config);

        $corePath = MODX_CORE_PATH . 'components/stripe/';
        $assetsUrl = MODX_ASSETS_URL . 'components/stripe/';

        $this->config = array_merge([
            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',
            'processorsPath' => $corePath . 'processors/',

            'connectorUrl' => $assetsUrl . 'connector.php',
            'assetsUrl' => $assetsUrl,
            'cssUrl' => $assetsUrl . 'css/',
            'jsUrl' => $assetsUrl . 'js/',
        ], $config);

        $this->modx->addPackage('stripe', $this->config['modelPath']);
        $this->modx->lexicon->load('stripe:default');
        $this->modx->lexicon->load('stripe:setting');

    }

    public function send(msOrder $order)
    {
        $confirm = $this->modx->getOption('stripe_confirm_page');
        if(!$confirm){
            return ;
        }
        //TODO Сделать проверку на контекст
        $link = $this->modx->makeUrl($confirm, 'web',['order' => $order->get('id')],'full');

        return $this->success('', ['redirect' => $link]);
    }

}
