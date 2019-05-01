<?php
/**
 * Created by PhpStorm.
 * User: hursittopal
 * Date: 2019-02-15
 * Time: 23:36
 */

namespace App\Service;


class Story extends AbstractService
{
    public function getStories($userId)
    {
        $client = $this->getClient();

        $storiesResult = $client->get('https://www.instagram.com/graphql/query/?query_hash=45246d3fe16ccc6577e0bd297a5db1ab&variables={"reel_ids":["' . $userId . '"],"tag_names":[],"location_ids":[],"highlight_reel_ids":[],"precomposed_overlay":false}', [
            'headers' => [
                "user-agent" => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.181 Safari/537.36",
            ]
        ]);

        return json_decode($storiesResult->getBody()->getContents(), true);
    }
}