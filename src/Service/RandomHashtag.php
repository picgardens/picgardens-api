<?php
/**
 * Created by PhpStorm.
 * User: hursittopal
 * Date: 2019-02-16
 * Time: 00:57
 */

namespace App\Service;


use Doctrine\DBAL\Connection;

class RandomHashtag
{
    const DATABASE = 'instagram_new';
    const COLLECTION = '404_pages';

    /** @var Hashtag $hashtagService */
    private $hashtagService;

    /** @var \MongoClient $mongoClient */
    private $mongoClient;

    /** @var Connection $connection */
    private $connection;

    /**
     * Api constructor.
     */
    public function __construct(Hashtag $hashtagService, \MongoClient $mongoClient, Connection $connection)
    {
        $this->hashtagService = $hashtagService;
        $this->mongoClient = $mongoClient;
        $this->connection = $connection;
    }

    public function prepare($key)
    {
        if($item = $this->mongoClient->{self::DATABASE}->{self::COLLECTION}->findOne(['key' => $key])) {
            return [
                'key' => $key,
                'hashtag' => $item['hashtag'],
                'result' => json_decode($item['result'], true)
            ];

            return $item;
        }

        $hashtag = $this->generateHashtag();

        $result = $this->hashtagService->hashtag($hashtag);

        $item = [
            'key' => $key,
            'hashtag' => $hashtag,
            'result' => json_encode($result)
        ];

        $this->mongoClient->{self::DATABASE}->{self::COLLECTION}->insert($item);

        return [
            'key' => $key,
            'hashtag' => $hashtag,
            'result' => $result
        ];
    }

    private function generateHashtag()
    {
        $id = rand(0, 200000);

        return $this->connection->fetchColumn("select name from hashtag where id < {$id} order by id desc limit 1 ");
    }
}