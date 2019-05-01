<?php

namespace App\Controller;

use App\Service\Search;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    /**
     * @Route("/search/{query}/")
     */
    public function index(Search $search, $query)
    {
        $data = $search->search($query);

        return new JsonResponse($data);
    }
}
