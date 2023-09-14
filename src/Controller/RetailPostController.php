<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RetailPostController extends AbstractController
{
    #[Route('/retailpost', name: 'app_retail_post')]
    public function index(): Response
    {
        return $this->render('retail_post/index.html.twig', [
            'controller_name' => 'RetailPostController',
        ]);
    }
}
