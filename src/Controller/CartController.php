<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartController extends AbstractController
{
    public function show_cart(SessionInterface $session, ProductRepository $productRepository, OrderRepository $orderRepository, EntityManagerInterface $manager): Response
    {
        $panier = $session->get('panier', []); //on récupère le panier (ou un tableau vide si le panier est null)

        $panier_with_data = []; //va stocker le panier et toutes les données nécessaires

        $total = 0; //montant total du panier

        foreach ($panier as $id => $quantity) //pour chaque élément du panier
        {
            $panier_with_data[$id] =
            [
                'product' => $productRepository->find($id), //on récupère les données du produit correspondant
                'quantity' => $quantity //et on garde sa quantité
            ];

            $total += $productRepository->find($id)->getPrice() * $quantity;
            $session->set('total', $total);
        }

        $order = $orderRepository->findOneBySessionId($session->getId()); //on regarde si la commande existe déjà

        if ($order == []) //si ce n'est pas le cas
        {
            //on crée une commande
            $order = new Order();
            $order->setIsRegistered($this->isGranted('ROLE_USER'));
            $order->setSessionId((int)$session->getId());
            $order->setStatus(0);
            $order->setTotal($total);
        }
        else
        {
            $order->setTotal($total);
        }

        $manager->persist($order);
        $manager->flush();



        return $this->render('pages/cart.html.twig', [ //on affiche le panier
            'panier' => $panier_with_data,
            'total' => $total
        ]);
    }

    public function add_to_cart($product_id, Request $request, SessionInterface $session, ProductRepository $productRepository): Response
    {
        $panier = $session->get('panier', []); //on récupère le panier (ou un tableau vide si le panier est null)
        $product_quantity = $request->get('product_quantity'); //on récupère la quantité de produit dans le POST

        if (!empty($panier[$product_id]))
            $panier[$product_id] += $product_quantity; //si le panier a déjà le produit on ajoute la quantité
        else
            $panier[$product_id] = $product_quantity; //sinon on lui donne la quantité

        $session->set('panier', $panier);

        return $this->redirectToRoute('show_cart'); //on affiche le panier
    }

    public function remove_from_cart($product_id, SessionInterface $session)
    {
        $panier = $session->get('panier', []);
        unset($panier[$product_id]);
        $session->set('panier', $panier);

        return $this->redirectToRoute('show_cart'); //on affiche le panier
    }
}
