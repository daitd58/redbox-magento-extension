<?php
/**
 * (c) Redbox Parcel Lockers <thamer@redboxsa.com>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by Redbox Technologies, <thamer@redboxsa.com>
 */

namespace Redbox\Shipping\Api;

interface PointServiceInterface
{


    /**
     * Get list points
     *
     * @param  float $lat
     * @param  float $lng
     * @return array
     */
    public function getPoints($lat, $lng);


}//end interface
