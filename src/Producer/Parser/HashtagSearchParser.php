<?php
/**
 * Created by PhpStorm.
 * User: hursittopal
 * Date: 2019-03-10
 * Time: 17:41
 */

namespace App\Producer\Parser;


class HashtagSearchParser
{
    public static function parse($result)
    {
        $hashtags = [];

        foreach ($result as $hashtag) {
            $hashtags[] = [
                'hashtag' => $hashtag['hashtag']['name'],
                'media_count' => $hashtag['hashtag']['media_count']
            ];
        }

        return $hashtags;
    }
}