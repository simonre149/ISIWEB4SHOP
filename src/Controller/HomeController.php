<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController
{

    public function home(ProductRepository $productRepository): Response
    {
        $all_products = $productRepository->findAll();

        return $this->render('pages/home.html.twig', [
            'all_products' => $all_products
        ]);
    }
}
