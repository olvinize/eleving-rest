<?php

namespace App\Controller\Api;

use App\Repository\ProductRepository;
use App\Utils\Helper;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/products', name: 'api.products.')]
class ProductsController extends ApiController
{

    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository, Helper $helper, RateLimiterFactory $apiLimiter)
    {
        $this->productRepository = $productRepository;
        parent::__construct($helper, $apiLimiter);
    }

    #[Route("", name: 'list', methods: ["GET"])]
    public function index(Request $request): JsonResponse
    {
        $this->checkRPS($request);
        $orders = $this->productRepository->findAll();
        $data = [];
        foreach ($orders as $order) {
            $data[] = $order->toArray(false);
        }
        return $this->helper->jsonResponse($data);
    }
}