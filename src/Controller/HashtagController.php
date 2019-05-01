<?php

namespace App\Controller;

use App\Service\Hashtag;
use App\Service\RandomHashtag;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class HashtagController extends AbstractController
{
    /**
     * @Route("/random-hashtag/{key}/")
     */
    public function random(RandomHashtag $randomHashtag, $key)
    {
        $data = $randomHashtag->prepare($key);

        return new JsonResponse($data);
    }

    /**
     * @Route("/hashtag-search/{query}/")
     */
    public function index(Hashtag $hashtag, $query)
    {
        $data = $hashtag->search($query);

        return new JsonResponse($data);
    }

    /**
     * @Route("/hashtag/{tag}/")
     */
    public function show(Hashtag $hashtag, $tag)
    {
        $data = $hashtag->hashtag($tag);

        return new JsonResponse($data);
    }

    /**
     * @Route("/popular-hashtags/")
     */
    public function popular(Connection $connection)
    {
        $results = $connection->fetchAll('select * from hashtag order by media_count desc limit 240');

        return new JsonResponse($results);
    }
}
