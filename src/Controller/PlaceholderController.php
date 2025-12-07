<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PlaceholderController extends AbstractController
{
    #[Route('/placeholder', name: 'placeholder')]
    public function index(): Response
    {
        return $this->render('placeholder.html.twig');
    }
}