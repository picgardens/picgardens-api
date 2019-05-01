<?php

namespace App\Controller;

use App\Service\Hashtag;
use App\Service\User;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/user/{username}/")
     */
    public function index(User $user, Hashtag $hashtag, $username)
    {
        $data = $user->getUser($username);

        if($data['user']['is_private']) {
            $data['recents'] = $hashtag->hashtag('love');
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/user-pagination/{username}/")
     */
    public function pagination(User $user, $username, Request $request)
    {
        $data = $user->getUser(
            $username,
            $request->get('user_id', null),
            $request->get('gis', null),
            $request->get('next_id', null)
        );

        return new JsonResponse($data);
    }

    /**
     * @Route("/popular-users/")
     */
    public function popular(Connection $connection)
    {
        $results = $connection->fetchAll('select * from user order by follower_count
 desc limit 300');

        return new JsonResponse($results);
    }
}
