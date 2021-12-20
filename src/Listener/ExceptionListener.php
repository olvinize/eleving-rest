<?php

namespace App\Listener;

use App\Utils\Helper;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;


class ExceptionListener
{
    private Helper $helper;

    public function __construct(Helper $helper)
    {
        $this->helper = $helper;
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $throwable = $event->getThrowable();

        if (method_exists($throwable, 'getStatusCode')) {
            $statusCode = $throwable->getStatusCode();
        } else {
            $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
        }
        $event->setResponse($this->helper->jsonResponse(null, $throwable->getMessage(), false, $statusCode));
    }
}