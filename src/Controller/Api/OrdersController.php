<?php

namespace App\Controller\Api;

use App\Repository\OrderRepository;
use App\Utils\Helper;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/orders', name: 'api.orders.')]
class OrdersController extends ApiController
{
    private OrderRepository $orderRepository;

    public function __construct(OrderRepository $orderRepository, Helper $helper, RateLimiterFactory $apiLimiter)
    {
        $this->orderRepository = $orderRepository;
        parent::__construct($helper, $apiLimiter);
    }

    #[Route('', name: 'list', methods: ["GET"])]
    public function index(Request $request): JsonResponse
    {
        $this->checkRPS($request);
        $status = $request->query->all('status');
        $criteria = [];
        if ($status) {
            $criteria['status'] = $status;
        }
        $orders = $this->orderRepository->findBy($criteria, ['id' => 'desc']);
        $data = [];
        foreach ($orders as $order) {
            $data[] = $order->toArray(false);
        }
        return $this->helper->jsonResponse($data);
    }
}