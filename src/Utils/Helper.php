<?php

namespace App\Utils;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class Helper
{
    public function jsonResponse(mixed $value, string $message = null, bool $success = true, int $code = 200): JsonResponse
    {
        $data = [
            'success' => $success,
            'code' => $code,
            'value' => $value
        ];
        if ($message) {
            $data['message'] = $message;
        }
        return new JsonResponse($data); //, $code);
    }

    public function getFormErrors(FormInterface $form): array
    {
        $errors = array();
        foreach ($form->getErrors() as $key => $error) {
            $template = $error->getMessageTemplate();
            $parameters = $error->getMessageParameters();

            foreach ($parameters as $var => $value) {
                $template = str_replace($var, $value, $template);
            }

            $errors[$key] = $template;
        }
        if ($form->count()) {
            foreach ($form as $child) {
                if (!$child->isValid()) {
                    $errors[$child->getName()] = $this->getFormErrors($child);
                }
            }
        }
        return $errors;
    }
}