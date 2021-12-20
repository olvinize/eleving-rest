<?php

namespace App\Controller\Api;

use App\Utils\Helper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\RateLimiter\RateLimiterFactory;

abstract class ApiController extends AbstractController
{
    protected Helper $helper;
    protected RateLimiterFactory $apiLimiter;

    public function __construct(Helper $helper, RateLimiterFactory $apiLimiter)
    {
        $this->helper = $helper;
        $this->apiLimiter = $apiLimiter;
    }

    protected function checkRPS(Request $request)
    {
        $limiter = $this->apiLimiter->create($request->getClientIp());
        if (false === $limiter->consume(1)->isAccepted()) {
            throw new TooManyRequestsHttpException(null, 'You have exceed RPS limit, please try in one minute');
        }
    }
}