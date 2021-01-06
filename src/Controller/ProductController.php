<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractController
{

    public function show_product($id, ProductRepository $productRepository): Response
    {
        $product = $productRepository->find($id);

        return $this->render('pages/product.html.twig', [
            'product' => $product
        ]);
    }
}
