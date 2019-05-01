<?php
/**
 * Created by PhpStorm.
 * User: hursittopal
 * Date: 2019-02-15
 * Time: 23:36
 */

namespace App\Service;


class Location extends AbstractService
{
    public function location($id, $slug)
    {
        $url = 'https://www.instagram.com/explore/locations/' . $id . '/' . $slug . '/';

        $result = $this->makeRequest($url, false);

        preg_match('/sharedData = (.*)\;<\/script>/', $result, $matches);

        $this->checkMatches($url, $matches, $result);

        $result = json_decode($matches[1], true);

        $this->produce('location_show', [
            'result' => $result['entry_data']['LocationsPage'][0]['graphql']['location'],
        ]);

        return [
            'location' => $result['entry_data']['LocationsPage'][0]['graphql']['location'],
            'gis' => $result['rhx_gis']
        ];
    }

    public function paginate($id, $slug, $endcursor, $gis = null) {

        $params = [
            'id' => $id,
            'first' => 12,
            'after' => $endcursor
        ];

        $params = json_encode($params);

        $gis = md5(sprintf('%s:%s', $gis, $params));

        $client = $this->getClient(['verify' => false, 'cookie' => true, 'debug' => true]);

        $url = 'https://www.instagram.com/explore/locations/' . $id . '/' . $slug . '/';

        $client->get($url);

        $options = [
            'headers' => [
                "user-agent" => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.181 Safari/537.36",
                "x-instagram-gis" => $gis,
                "x-requested-with" => "XMLHttpRequest",
                "x-ig-app-id" => "936619743392459",
                #"cookie" => ''
            ]
        ];

        $url = self::BASE_URL . '/graphql/query/?query_hash=1b84447a4d8b6d6d0426fefb34514485&variables=' . $params;

        $response = $client->get($url, $options);

        $result = json_decode($response->getBody()->getContents(), true);

        return $result['data'];
    }
}