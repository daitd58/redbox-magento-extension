<?php
/**
 * (c) Redbox Parcel Lockers <thamer@redboxsa.com>
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * Built by Redbox Technologies, <thamer@redboxsa.com>
 */

namespace Redbox\Shipping\Model;
use Redbox\Shipping\Api\PointServiceInterface;
use Redbox\Shipping\Helper\Points;
use Magento\Framework\HTTP\Client\Curl;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

/**
 * Class PointService
 *
 * @package Redbox\Shipping\Model
 */
class PointService implements PointServiceInterface
{

    /**
     * @var Points
     */
    private $helper;

    /**
     * @var Curl
     */
    private $curl;

    /**
     * @var PsrLoggerInterface
     */
    private $logger;


    /**
     * PointService constructor
     *
     * @param Points $helper
     * @param Curl $curl
     */
    public function __construct(
        PsrLoggerInterface $logger,
        Points $helper,
        Curl $curl
    ) {
        $this->curl   = $curl;
        $this->helper = $helper;
        $this->logger = $logger;

    }//end __construct()


    /**
     * Get list points
     *
     * @param  float $lat
     * @param  float $lng
     * @return array
     */
    public function getPoints($lat = 21.0500889, $lng = 105.7976686)
    {
        $apiToken   = $this->helper->getApiToken();
        $businessId = $this->helper->getBusinessId();
        $url = 'https://app.redboxsa.com/api/business/v1/get-points?lat=' . $lat . '&lng=' . $lng . '&distance=10000000';

        $this->logger->info('api token: ' . $apiToken);
        if ($apiToken) {
            $headers = [
                "Content-Type" => "application/json",
                "Authorization" => "Bearer " . $apiToken
            ];
            $this->curl->setHeaders($headers);
            $this->curl->get($url);
            $result = json_decode($this->curl->getBody(), true);

            return $result;
        } else {
            return [];
        }

    }//end getPoints()


}//end class
