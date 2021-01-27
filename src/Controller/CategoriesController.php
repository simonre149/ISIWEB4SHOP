<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class CategoriesController extends AbstractController
{
    public function show_category($category_id, CategoryRepository $repository): Response
    {
        $category = $repository->find($category_id);
        $products = $category->getProducts();

        return $this->render('pages/category.html.twig', [
            'category' => $category,
            'products' => $products
        ]);
    }
}
