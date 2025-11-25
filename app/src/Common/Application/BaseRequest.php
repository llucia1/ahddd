<?php
declare(strict_types=1);

namespace GridCP\Common\Application;

use Error;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class BaseRequest
{




    public function __construct(protected ValidatorInterface $validator)
    {
         $this->populate();

        if ($this->autoValidateRequest()) {
            $this->validate();
        }
    }

    protected function populate(): void
    {
        try {
            foreach ($this->getRequest()->toArray() as $property => $value) {
                if (property_exists($this, $property)) {
                    $this->{$property} = $value;
                }
            }
        }catch(Error $e){
            $response = new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
            $response->send();
        }
    }

    public function getRequest(): Request
    {
        return Request::createFromGlobals();
    }

    protected function autoValidateRequest(): bool
    {
        return true;
    }

    public function validate()
    {
        $errors = $this->validator->validate($this);

        $messages = ['message' => 'GridCP Validation Failed :(', 'errors' => []];

        /** @var ConstraintViolation */
        foreach ($errors as $message) {
            $messages['errors'][] = [
                'property' => $message->getPropertyPath(),
                'value' => $message->getInvalidValue(),
                'message' => $message->getMessage(),
            ];
        }

        if (count($messages['errors']) > 0) {
            $response = new JsonResponse($messages, Response::HTTP_BAD_REQUEST);
            $response->send();
        }
    }
}