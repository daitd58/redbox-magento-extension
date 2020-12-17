<?php

namespace Redbox\Shipping\Plugin;

class ConfigPlugin
{
    public function aroundSave(
        \Magento\Config\Model\Config $subject,
        \Closure $proceed,
        \Redbox\Shipping\Logger\Logger $logger
    ) {
        // your custom logic
        $logger->info('save---------');
        return $proceed();
    }
}