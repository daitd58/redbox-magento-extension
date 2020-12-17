<?php
/**
 * (c) Redbox Parcel Lockers <thamer@redboxsa.com>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by Redbox Technologies, <thamer@redboxsa.com>
 */

namespace Redbox\Shipping\Logger;

class Logger extends \Monolog\Logger
{
    // @codingStandardsIgnoreStart
    public function __construct($name, $handlers = [], $processors = [])
    {
        parent::__construct($name, $handlers, $processors);
    }
    // @codingStandardsIgnoreEnd
}
