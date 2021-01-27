<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class OrderController extends AbstractController
{
    public function order_address_page(): Response
    {
        if ($this->isGranted('ROLE_USER'))
        {
            $address_line1 = $this->getUser()->getAdd1();
            $address_line2 = $this->getUser()->getAdd2();
        }
        else
        {
            $address_line1 = null;
            $address_line2 = null;
            $address_line3 = null;
        }

        return $this->render('pages/order/order_address.html.twig', [
            'address_line1' => $address_line1,
            'address_line2' => $address_line2
        ]);
    }

    public function order_payment_page(Request $request, SessionInterface $session, OrderRepository $orderRepository, EntityManagerInterface $manager)
    {
        $line1 = $request->get('line1');
        $line2 = $request->get('line2');
        $postcode = $request->get('postcode');
        $city = $request->get('city');

        if (!$line1 && !$line2 && !$postcode && !$city && $this->isGranted('ROLE_USER')) //connecté et pas de formulaire
        {
            $line1 = $this->getUser()->getAdd1();
            $line2 = $this->getUser()->getAdd2();
            $postcode = $this->getUser()->getPostcode();
            $city = $this->getUser()->getCity();
        }

        $order = $orderRepository->findOneBySessionId($session->getId());
        $order->setStatus(1);
        $manager->persist($order);
        $manager->flush();

        return $this->render('pages/order/order_payment.html.twig', [
            'line1' => $line1,
            'line2' => $line2,
            'postcode' => $postcode,
            'city' => $city,
            'total' => $session->get('total')
        ]);
    }

    public function order_cancelled()
    {
        return $this->render('pages/order/order_cancelled.html.twig');
    }

    public function order_succeeded(OrderRepository $orderRepository, SessionInterface $session, EntityManagerInterface $manager)
    {
        /*
        if ($this->isGranted('ROLE_USER'))
        {
            $pdf = new FPDF();
            $pdf->AddPage();
            $pdf->SetFont('Arial','B',16);
            $pdf->Cell(40,10,'Hello World !');
            return $pdf->Output();
        }
        else dd('not connected');*/

        $order = $orderRepository->findOneBySessionId($session->getId());
        $order->setStatus(2);
        $order->setDate(new \DateTime());
        $order->setPaymentType("PayPal");
        $manager->persist($order);
        $manager->flush();

        return $this->render('pages/order/order_succeeded.html.twig');
    }

    public function order_succeeded_cheque(OrderRepository $orderRepository, SessionInterface $session, EntityManagerInterface $manager)
    {
        $session->start();
        $order = $orderRepository->findOneBySessionId($session->getId());
        $order->setStatus(2);
        $order->setDate(new \DateTime());
        $order->setPaymentType("Chèque");
        $manager->persist($order);
        $manager->flush();

        return $this->render('pages/order/order_succeeded_cheque.html.twig', [
            'total' => $session->get('total')
        ]);
    }

    public function order_sent($order_id, OrderRepository $orderRepository, EntityManagerInterface $manager)
    {
        $order = $orderRepository->find($order_id);
        $order->setStatus(10);

        $manager->persist($order);
        $manager->flush();

        return $this->redirectToRoute('home');
    }
}
