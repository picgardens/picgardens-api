<?php

namespace App\Controller;

use App\Service\Media;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class MediaController extends AbstractController
{
    /**
     * @Route("/media/{id}/")
     */
    public function index(Media $media, $id)
    {
        $data = $media->media($id);

        return new JsonResponse($data);
    }
}
