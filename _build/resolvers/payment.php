<?php
/** @var xPDOTransport $transport */
/** @var array $options */
if ($transport->xpdo) {
    /** @var modX $modx */
    $modx =& $transport->xpdo;

    /** @var miniShop2 $miniShop2 */
    if (!$miniShop2 = $modx->getService('miniShop2')) {
        $modx->log(modX::LOG_LEVEL_ERROR, '[mspPaybox] Could not load miniShop2');

        return false;
    }
    if (!property_exists($miniShop2, 'version') || version_compare($miniShop2->version, '2.4.0-pl', '<')) {
        $modx->log(modX::LOG_LEVEL_ERROR,
            '[mspPaybox] You need to upgrade miniShop2 at least to version 2.4.0-pl');

        return false;
    }

    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            $miniShop2->addService('payment', 'StripePayment', '{core_path}components/stripe/model/stripepayment.class.php');
            /** @var msPayment $payment */
            if (!$payment = $modx->getObject('msPayment', array('class' => 'StripePayment'))) {
                $payment = $modx->newObject('msPayment');
                $payment->fromArray(array(
                    'name' => 'Stripe',
                    'active' => false,
                    'class' => 'StripePayment',
                    'rank' => $modx->getCount('msPayment'),
                    'logo' => MODX_ASSETS_URL . 'components/stripe/stripe.png',
                ), '', true);
                $payment->save();
            }

            /**
             * Создаем ресурс для редиректа и отправки на оплату
             */

            $content = "
              <!doctype html>
            <html lang=\"en\">
            <head>
                <meta charset=\"UTF-8\">
                <meta name=\"viewport\"
                      content=\"width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0\">
                <meta http-equiv=\"X-UA-Compatible\" content=\"ie=edge\">
                <script src=\"https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js\"></script>
                <script src=\"https://js.stripe.com/v3/\"></script>
                <script src=\"/assets/components/stripe/web/js/stripe.js\"></script>
                <title>Оплата Stripe</title>
            </head>
            <body>
            
            </body>
            </html>
            ";

            $count = $modx->getCount('modResource', ['pagetitle' => 'Оплата Stripe']);

            $processor = $count > 0
                ? 'update'
                : 'create';

            $resource = $modx->runProcessor('resource/'.$processor, [
                'pagetitle' => 'Оплата Stripe',
                'template' => 0,
                'content' => $content,
                'published' => true,
                'hidemenu' => true
            ]);

            if($resource->isError()){
                $modx->log(MODX_LOG_LEVEL_ERROR, 'Error create resource Stripe payment');
            }

            $id = $resource->response['object']['id'];
            $modx->setOption('stripe_confirm_page', $id);

            break;

        case xPDOTransport::ACTION_UNINSTALL:
            $miniShop2->removeService('payment', 'msPayment');
            $modx->removeCollection('msPayment', array('class' => 'msPayment'));
            break;
    }
}
return true;