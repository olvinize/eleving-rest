<?php

namespace App\Controller\Api;

use App\Entity\Order;
use App\Form\NewOrderType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/shipping', name: 'api.shipping.')]
class ShippingController extends ApiController
{
    #[Route("/calc", name: 'calc', methods: ["POST"])]
    public function estimate(Request $request): JsonResponse
    {
        $this->checkRPS($request);
        $order = new Order();
        $order->setCreated(new \DateTime());

        $form = $this->createForm(NewOrderType::class, $order);
        $form->submit($request->request->all());
        if ($form->isValid()) {
            $order->setTotals();
            return $this->helper->jsonResponse([
                'deliveryTotal' => $order->getDeliveryTotal(),
                'productsTotal' => $order->getProductsTotal(),
                'grandTotal' => $order->getGrandTotal()
            ]);
        } else {
            return $this->helper->jsonResponse($this->helper->getFormErrors($form), 'Unable to calculate price', false);
        }
    }
}
