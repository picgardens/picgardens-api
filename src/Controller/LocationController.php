<?php

namespace App\Controller;

use App\Service\Location;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class LocationController extends AbstractController
{
    /**
     * @Route("/location/{id}/{slug}/")
     */
    public function index(Location $location, $id, $slug)
    {
        $data = $location->location($id,$slug);

        return new JsonResponse($data);
    }
    /**
     * @Route("/location-paginate/{id}/{slug}/{end_cursor}/{gis}/")
     */
    public function paginate(Location $location, $id, $slug, $end_cursor, $gis)
    {
        $data = $location->paginate($id, $slug, $end_cursor, $gis);

        return new JsonResponse($data);
    }
}
