<?php
/**
 * Created by PhpStorm.
 * User: hursittopal
 * Date: 2019-03-10
 * Time: 17:41
 */

namespace App\Producer\Parser;


class SearchParser
{
    public static function parse($result)
    {
        $hashtags = $locations = $users = [];

        foreach ($result['users'] as $user) {
            $users[] = [
                'fullname' => $user['user']['full_name'],
                'username' => $user['user']['username'],
                'follower_count' => $user['user']['follower_count'],
                'profile_pic_url' => $user['user']['profile_pic_url']
            ];
        }

        foreach ($result['hashtags'] as $hashtag) {
            $hashtags[] = [
                'hashtag' => $hashtag['hashtag']['name'],
                'media_count' => $hashtag['hashtag']['media_count']
            ];
        }


        foreach ($result['places'] as $place) {
            $locations[] = $place['place']['location']['pk'];
        }

        return [$users, $hashtags, $locations];
    }
}