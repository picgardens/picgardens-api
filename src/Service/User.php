<?php
/**
 * Created by PhpStorm.
 * User: hursittopal
 * Date: 2019-02-15
 * Time: 23:36
 */

namespace App\Service;


use App\Parameters\RequestParameters;

class User extends AbstractService
{
    public function getUser($username, $id = null, $gis = null, $nextId = null)
    {
        if ($id) {
            $params = [
                'id' => $id,
                'first' => 12,
                'after' => $nextId
            ];

            $params = json_encode($params);

            $url = self::BASE_URL . '/graphql/query/?query_hash=42323d64886122307be10013ad2dcc44&variables=' . $params;

            $gis = md5(sprintf('%s:%s', $gis, $params));

            $options = [
                'headers' => [
                    "user-agent" => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.181 Safari/537.36",
                    "x-instagram-gis" => $gis,
                    "x-requested-with" => "XMLHttpRequest",
                ]
            ];

            $response = $this->getClient(['verify' => false])->get($url, $options);

            $result = json_decode($response->getBody()->getContents(), true);

            $this->produce('user_show', [
                'result' => $result['data'],
            ]);

            return $result['data'];
        } else {
            $url = self::BASE_URL . "/$username/";

            $options = [
                'headers' => [
                    "user-agent" => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.181 Safari/537.36",
                ]
            ];

            $response = $this->getClient(['verify' => false])->get($url, $options);

            $response = $response->getBody()->getContents();

            preg_match('/sharedData = (.*)\;<\/script>/', $response, $matches);

            $this->checkMatches($url, $matches);

            $data = json_decode($matches[1], true);

            $gis = $data['rhx_gis'];
            $csrfToken = $data['config']['csrf_token'];

            $userData = $data['entry_data']['ProfilePage'][0]['graphql'];

            $userData['user']['gis'] = $gis;
            $userData['user']['csrf_token'] = $csrfToken;
            $userData['user']['next_id'] = $userData['user']['edge_owner_to_timeline_media']['page_info']['end_cursor'];

            try {
                if (RequestParameters::$isBot) {
                    $userData['stories'] = [];
                } else {
                    $stories = $this->getStories($userData['user']['id']);

                    $userData['stories'] = $stories['data']['reels_media'][0]['items'];
                }
            } catch (\Exception|\Error $exception) {
                $userData['stories'] = [];
            }

            $this->produce('user_show', [
                'result' => $userData,
            ]);

            return $userData;
        }
    }
}