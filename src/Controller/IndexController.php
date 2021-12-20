<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('')]
class IndexController extends AbstractController
{

    #[Route('', name: 'home', methods: ["GET", "POST"])]
    public function index(Request $request, ProductRepository $productRepository): Response
    {

        return $this->render('index.html.twig');
    }
}