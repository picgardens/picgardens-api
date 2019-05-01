<?php
/**
 * Created by PhpStorm.
 * User: hursittopal
 * Date: 2019-03-10
 * Time: 17:14
 */

namespace App\Consumer;


use Doctrine\DBAL\Connection;
use PhpAmqpLib\Message\AMQPMessage;

class InstagramDataStorageConsumer
{
    /** @var Connection */
    private $connection;

    /**
     * InstagramDataStorageConsumer constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function execute(AMQPMessage $msg)
    {
        sleep(2);

        $body = unserialize($msg->getBody());

        $this->connection->beginTransaction();

        if (!empty($body['users'])) {
            foreach ($body['users'] as $user) {
                try {
                    $this > $this->connection->executeQuery(
                        "INSERT INTO `user` (`id`, `username`, `follower_count`, `profile_pic_url`, `full_name`) VALUES (NULL, '{$user['username']}', '{$user['follower_count']}', '{$user['profile_pic_url']}', '{$user['fullname']}') ON DUPLICATE KEY UPDATE follower_count = VALUES(follower_count), profile_pic_url = VALUES(profile_pic_url)"
                    );
                } catch (\Exception|\Error $exception) {

                }
            }
        }

        if (!empty($body['hashtags'])) {
            foreach ($body['hashtags'] as $hashtag) {
                try {
                    $this > $this->connection->executeQuery(
                        "INSERT INTO `hashtag` (`id`, `name`, `media_count`) VALUES (NULL, '{$hashtag['hashtag']}', '{$hashtag['media_count']}')  ON DUPLICATE KEY UPDATE media_count = VALUES(media_count)"
                    );
                } catch (\Exception|\Error $exception) {

                }
            }
        }

        if (!empty($body['locations'])) {
            foreach ($body['locations'] as $location) {
                try {
                    $this > $this->connection->executeQuery("INSERT INTO `location` (`id`, `pk_id`) VALUES (NULL, '{$location}')");
                } catch (\Exception|\Error $exception) {

                }
            }
        }

        $this->connection->commit();
    }
}