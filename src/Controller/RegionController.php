<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegionController extends AbstractController
{
    /**
     * @Route("/region", name="region")
     */
    public function index(): Response
    {
        return $this->render('region/index.html.twig', [
            'controller_name' => 'RegionController',
        ]);
    }
}
