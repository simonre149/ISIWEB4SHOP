<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController
{

    public function home(ProductRepository $productRepository, OrderRepository $orderRepository): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) //pour les admins
        {
            $all_orders = $orderRepository->findAllOrderedByStatus();

            return $this->render('pages/admin_home.html.twig', [
                'all_orders' => $all_orders
            ]);
        }
        else //pour les autres
        {
            $all_products = $productRepository->findAll();

            return $this->render('pages/home.html.twig', [
                'all_products' => $all_products
            ]);
        }
    }
}
