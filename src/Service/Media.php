<?php
/**
 * Created by PhpStorm.
 * User: hursittopal
 * Date: 2019-02-15
 * Time: 23:36
 */

namespace App\Service;

use App\Producer\InstagramDataStorageProducer;

class Media extends AbstractService
{
    /** @var $userService User */
    private $userService;

    /**
     * Media constructor.
     * @param $userService
     */
    public function __construct(InstagramDataStorageProducer $producer, User $user)
    {
        $this->userService = $user;

        parent::__construct($producer);
    }


    public function media($shortCode)
    {
        $url = "https://www.instagram.com/p/$shortCode/";

        $result = $this->makeRequest($url, false);

        preg_match('/PostPage\"\:\[(.*)\]\}/', $result, $matches);

        $this->checkMatches($url, $matches, $result);

        $result = json_decode($matches[1], true);

        $username = $result['graphql']['shortcode_media']['owner']['username'];

        $user = $this->userService->getUser($username);

        $result['user'] = $user['user'];

        $this->produce('media_show', [
            'result' => $result,
        ]);

        return $result;
    }
}