<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionController extends AbstractController
{
    public function showException(Request $request, \Throwable $exception): Response
    {
        $statusCode = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 500;

        return $this->render("bundles/TwigBundle/Exception/error{$statusCode}.html.twig", [
            'exception' => $exception,
            'status_code' => $statusCode,
        ]);
    }
}
