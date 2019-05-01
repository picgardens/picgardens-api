<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class StoryController extends AbstractController
{
    /**
     * @Route("/story")
     */
    public function index()
    {
        return $this->render('story/index.html.twig', [
            'controller_name' => 'StoryController',
        ]);
    }
}
