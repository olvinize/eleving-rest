<?php

namespace App\Controller\Api;

use App\Repository\CourierRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/couriers', name: 'api.couriers.')]
class CouriersController extends ApiController
{
    #[Route("", name: 'list', methods: ["GET"])]
    public function index(CourierRepository $courierRepository): JsonResponse
    {
        $couriers = $courierRepository->findAll();
        $data = [];
        foreach ($couriers as $courier) {
            $data[] = $courier->toArray(false);
        }
        return $this->helper->jsonResponse($data);
    }
}