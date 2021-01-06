<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartController extends AbstractController
{
    public function show_cart(Request $request, SessionInterface $session, ProductRepository $productRepository): Response
    {
        if ($this->isGranted('ROLE_USER')) //SI L'UTILISATEUR EST CONNECTE
        {
            dd("Utilisateur connecté non rempli");
        }
        else //SINON ON PASSE PAR LA SESSION
        {
            $panier = $session->get('panier', []); //on récupère le panier (ou un tableau vide si le panier est null)

            $panier_with_data = []; //va stocker le panier et toutes les données nécessaires
            foreach ($panier as $id => $quantity) //pour chaque élément du panier
            {
                $panier_with_data[$id] =
                [
                    'product' => $productRepository->find($id), //on récupère les données du produit correspondant
                    'quantity' => $quantity //et on garde sa quantité
                ];
            }
        }

        return $this->render('pages/cart.html.twig', [ //on affiche le panier
            'panier' => $panier_with_data
        ]);
    }

    public function add_to_cart($product_id, Request $request, SessionInterface $session): Response
    {
        if ($this->isGranted('ROLE_USER')) //SI L'UTILISATEUR EST CONNECTE
        {
            dd("Utilisateur connecté non rempli");
        }
        else //SINON ON PASSE PAR LA SESSION
        {
            $panier = $session->get('panier', []); //on récupère le panier (ou un tableau vide si le panier est null)
            $product_quantity = $request->get('product_quantity'); //on récupère la quantité de produit dans le POST

            if (!empty($panier[$product_id]))
                $panier[$product_id] += $product_quantity; //si le panier a déjà le produit on ajoute la quantité
            else
                $panier[$product_id] = $product_quantity; //sinon on lui donne la quantité

            $session->set('panier', $panier);
        }

        return $this->redirectToRoute('show_cart'); //on affiche le panier
    }
}
