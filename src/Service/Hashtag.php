<?php
/**
 * Created by PhpStorm.
 * User: hursittopal
 * Date: 2019-02-15
 * Time: 23:36
 */

namespace App\Service;


class Hashtag extends AbstractService
{
    public function search($query)
    {
        $hashtagsUrl = self::SEARCH_URL . '%23' . $query;

        $hashtagsResponse = $this->makeRequest($hashtagsUrl, true);

        $hashtags = $hashtagsResponse['hashtags'];

        $hashtagSort = function ($a, $b) {
            return ((int)$a['hashtag']['media_count'] > (int)$b['hashtag']['media_count']) ? -1 : 1;
        };

        if (is_array($hashtags)) {
            usort($hashtags, $hashtagSort);
        }

        $this->produce('hashtag_search', [
            'result' => $hashtags
        ]);

        return [
            'hashtags' => $hashtags
        ];
    }

    public function hashtag($tag)
    {
        $url = "https://www.instagram.com/explore/tags/$tag/";

        $response = $this->makeRequest($url, false);

        preg_match('/TagPage\"\:\[(.*)\]\}/', $response, $matches);

        $this->checkMatches($url, $matches, $response);

        $response = json_decode($matches[1], true);

        $recentMedias = $response['graphql']['hashtag']['edge_hashtag_to_media']['edges'];

        $popularMedias = $response['graphql']['hashtag']['edge_hashtag_to_top_posts']['edges'];

        if ($response['graphql']['hashtag']['edge_hashtag_to_media']['page_info']['has_next_page']) {
            $endCursor = $response['graphql']['hashtag']['edge_hashtag_to_media']['page_info']['end_cursor'];
        } else {
            $endCursor = null;
        }

        $this->produce('hashtag_show', [
            'result' => [
                'recents' => $recentMedias,
                'populars' => $popularMedias
            ]
        ]);

        return array(
            'end_cursor' => $endCursor,
            'recents' => $recentMedias,
            'populars' => $popularMedias
        );
    }
}