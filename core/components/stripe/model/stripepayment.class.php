<?php
/**
 * https://stripe.com/docs/development/quickstart
 */
if (!class_exists('msPaymentInterface')) {
    /** @noinspection PhpIncludeInspection */
    require_once MODX_CORE_PATH . 'components/minishop2/model/minishop2/mspaymenthandler.class.php';
}


class StripePayment extends msPaymentHandler implements msPaymentInterface
{
    /** @var modX $modx */
    public $modx;
    public $order;


    /**
     * @param modX $modx
     * @param array $config
     */
    function __construct(xPDOObject $object, array $config = [])
    {
        parent::__construct($object, $config);

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
        $link = $this->modx->makeUrl($confirm, 'web',['order' => $order->get('id')],'full');

        return $this->success('', ['redirect' => $link]);
    }

}