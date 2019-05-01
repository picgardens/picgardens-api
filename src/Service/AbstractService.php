<?php
/**
 * Created by PhpStorm.
 * User: hursittopal
 * Date: 2019-02-15
 * Time: 23:35
 */

namespace App\Service;


use App\Producer\InstagramDataStorageProducer;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

class AbstractService
{
    /** @var InstagramDataStorageProducer */
    private $producer;

    CONST BASE_URL = "https://www.instagram.com";

    CONST SEARCH_URL = 'https://www.instagram.com/web/search/topsearch/?context=blended&rank_token=0.4596826017632842&query=';

    /**
     * AbstractService constructor.
     * @param InstagramDataStorageProducer $producer
     */
    public function __construct(InstagramDataStorageProducer $producer)
    {
        $this->producer = $producer;
    }

    public function getClient($options = [])
    {
        return new Client($options);
    }

    public function makeRequest($url, $isJson = true)
    {
        $options = [
            'headers' => [
                "accept-encoding:gzip" => "deflate, br",
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/59.0.3071.115 Safari/537.36',

            ]
        ];

        /** @var Response $response */
        $response = $this->getClient(['verify' => false, 'cookie' => true, 'http_errors' => false, 'timeout' => 10])->get(
            $url, $options
        );

        if ($isJson) {
            $data = json_decode($response->getBody()->getContents(), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('json parse exception');
            }

            return $data;
        }

        return $response->getBody()->getContents();
    }

    public function checkMatches($url, $matches, $response = null)
    {
        if (!isset($matches[1])) {
            throw new \Exception('Not matched url: ' . $url);
        }
    }

    public function produce($type, $data)
    {
        try {
            $this->producer->produce($type, $data);
        } catch (\Exception|\Error $exception) {

        }
    }
}