<?php
/**
 * Created by PhpStorm.
 * User: hursittopal
 * Date: 2019-03-10
 * Time: 17:14
 */

namespace App\Producer;


use App\Producer\Parser\HashtagSearchParser;
use App\Producer\Parser\SearchParser;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;

class InstagramDataStorageProducer
{
    /** @var ProducerInterface */
    private $producer;

    /**
     * InstagramDataStorageProducer constructor.
     * @param ProducerInterface $producer
     */
    public function __construct(ProducerInterface $producer)
    {
        $this->producer = $producer;
    }

    public function produce($type, $data)
    {
        $hashtags = $locations = $users = [];

        if($type === 'hashtag_search') {
            $hashtags =  HashtagSearchParser::parse($data['result']);
        } elseif($type === 'hashtag_show') {
            return;
        } elseif($type === 'search') {
            list($users, $hashtags, $locations) =  SearchParser::parse($data['result']);
        } elseif($type === 'user_show') {
            return;
        } elseif($type === 'media_show') {
            return;
        }

        if(empty($hashtags) && empty($users) && empty($locations)) {
            return;
        }

        $msg = array(
            'users' => $users,
            'locations' => $locations,
            'hashtags' => $hashtags,
        );

        $this->producer->publish(serialize($msg));
    }

    public function parseUsers()
    {

    }

    public function parseHashtags()
    {

    }

    public function parseLocations()
    {

    }
}