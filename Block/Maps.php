<?php
/**
  * (c) Redbox Parcel Lockers <thamer@redboxsa.com>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by Redbox Technologies, <thamer@redboxsa.com>
 */

namespace Redbox\Shipping\Block;

use Magento\Framework\View\Element\Template;
use Redbox\Shipping\Helper\Points;

class Maps extends Template
{

    /**
     * @var Points
     */
    private $helper;
    /** @var Template\Context  */
    private $context;

    /**
     * Constructor
     *
     * @param Template\Context $context
     * @param Points $helper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Points $helper,
        array $data = []
    ) {
        $this->context = $context;
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    public function isMapActive() {
        return $this->helper->isMapActive();
    }
}
