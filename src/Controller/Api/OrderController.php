<?php

namespace App\Controller\Api;

use App\Entity\Order;
use App\Form\NewOrderType;
use App\Repository\CourierRepository;
use App\Repository\OrderRepository;
use App\Utils\Helper;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/order', name: 'api.order.')]
class OrderController extends ApiController
{
    private OrderRepository $orderRepository;

    public function __construct(OrderRepository $orderRepository, Helper $helper, RateLimiterFactory $apiLimiter)
    {
        $this->orderRepository = $orderRepository;
        parent::__construct($helper, $apiLimiter);
    }

    #[Route("", name: 'place', methods: ["POST"])]
    public function create(Request $request): JsonResponse
    {
        $this->checkRPS($request);
        $order = new Order();
        $order->setCreated(new \DateTime());

        $form = $this->createForm(NewOrderType::class, $order);
        $form->submit($request->request->all());
        if ($form->isValid()) {
            $this->orderRepository->save($order);
            return $this->helper->jsonResponse($order->getId(), null, true, Response::HTTP_CREATED);
        }
        return $this->helper->jsonResponse($this->helper->getFormErrors($form), 'Please fix errors', false);
    }

    #[Route("/{id}", name: 'view', requirements: ['id' => '\d{1,10}'], methods: ["GET"])]
    public function view(int $id, Request $request): JsonResponse
    {
        $this->checkRPS($request);
        $order = $this->getOrder($id);
        return $this->helper->jsonResponse($order->toArray(true), null, Response::HTTP_FOUND);
    }

    #[Route("/{id}/setCourier/{courierId}",
        name: 'setCourier',
        requirements: ['id' => '\d{1,10}', 'courierId' => '\d{1,10}'],
        methods: ["POST"])]
    public function courier(int $id, int $courierId, Request $request, CourierRepository $courierRepository): JsonResponse
    {
        $this->checkRPS($request);
        $courier = $courierRepository->find($courierId);
        if ($courier) {
            $order = $this->getOrder($id);
            if ($order->getStatus() == Order::STATUS_PROCESSING) {
                $order->setStatus(Order::STATUS_DELIVERING);
                $order->setCourier($courier);
                $this->orderRepository->save($order);
            } else {
                throw new UnprocessableEntityHttpException('Order cannot be updated');
            }
        } else {
            throw $this->createNotFoundException('Courier not found');
        }

        return $this->helper->jsonResponse(null);
    }

    #[Route("/{id}/delivered", name: 'delivered', requirements: ['id' => '\d{1,10}'], methods: ["POST"])]
    public function delivered(int $id): JsonResponse
    {
        $order = $this->getOrder($id);
        $message = null;
        if ($order->getStatus() != Order::STATUS_DELIVERED) {
            $order->setStatus(Order::STATUS_DELIVERED);
            $order->setDelivered(new \DateTime());
            $this->orderRepository->save($order);
        } else {
            $message = 'Order already is delivered';
        }

        return $this->helper->jsonResponse(null, $message);
    }

    private function getOrder(int $id): Order
    {
        $order = $this->orderRepository->find($id);
        if (!$order) {
            throw $this->createNotFoundException('Order not found');
        }
        return $order;
    }

}