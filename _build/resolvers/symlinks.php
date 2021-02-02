<?php
/** @var xPDOTransport $transport */
/** @var array $options */
/** @var modX $modx */
if ($transport->xpdo) {
    $modx =& $transport->xpdo;

    $dev = MODX_BASE_PATH . 'Extras/Stripe/';
    /** @var xPDOCacheManager $cache */
    $cache = $modx->getCacheManager();
    if (file_exists($dev) && $cache) {
        if (!is_link($dev . 'assets/components/stripe')) {
            $cache->deleteTree(
                $dev . 'assets/components/stripe/',
                ['deleteTop' => true, 'skipDirs' => false, 'extensions' => []]
            );
            symlink(MODX_ASSETS_PATH . 'components/stripe/', $dev . 'assets/components/stripe');
        }
        if (!is_link($dev . 'core/components/stripe')) {
            $cache->deleteTree(
                $dev . 'core/components/stripe/',
                ['deleteTop' => true, 'skipDirs' => false, 'extensions' => []]
            );
            symlink(MODX_CORE_PATH . 'components/stripe/', $dev . 'core/components/stripe');
        }
    }
}

return true;