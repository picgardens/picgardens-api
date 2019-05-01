<?php
/**
 * Created by PhpStorm.
 * User: hursittopal
 * Date: 2019-02-15
 * Time: 23:36
 */

namespace App\Service;


class Search extends AbstractService
{
    public function search($query) {
        $url = self::SEARCH_URL . $query;

        $response = $this->makeRequest($url, true);

        $this->produce('search', [
            'result' => $response,
        ]);

        return [
            'data' => $response
        ];
    }
}