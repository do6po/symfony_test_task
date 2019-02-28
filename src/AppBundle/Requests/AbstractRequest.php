<?php
/**
 * Created by PhpStorm.
 * User: box
 * Date: 25.02.19
 * Time: 19:14
 */

namespace AppBundle\Requests;


use AppBundle\Exceptions\RequestValidationErrorException;
use AppBundle\Interfaces\FillableFromRequestInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class AbstractRequest
 * @package AppBundle\Requests
 */
abstract class AbstractRequest
{
    protected $errors = [];

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * AbstractRequest constructor.
     * @param ContainerInterface $container
     * @param RequestStack $requestStack
     * @throws RequestValidationErrorException
     */
    public function __construct(ContainerInterface $container, RequestStack $requestStack)
    {
        $this->container = $container;

        $this->request = $requestStack->getCurrentRequest();

        $this->processed();
    }

    /**
     * @throws RequestValidationErrorException
     */
    public function processed()
    {
        $this->validate();

        if ($this->hasErrors()) {
            throw new RequestValidationErrorException($this->getErrors());
        }
    }

    abstract public function getFillableFromRequestObject(): FillableFromRequestInterface;

    public function validate()
    {
        $validator = $this->getValidator();

        $dto = $this->getFillableFromRequestObject();

        $dto->fillByRequest($this->request);
        $violations = $validator->validate($dto);

        $this->extractErrors($violations);
    }

    public function extractErrors(ConstraintViolationListInterface $violationList)
    {
        foreach ($violationList as $item) {
            /** @var ConstraintViolation $item */
            $this->errors[$item->getPropertyPath()][] = $item->getMessage();
        }
    }

    public function hasErrors(): bool
    {
        return count($this->getErrors()) > 0;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    private function getValidator(): ValidatorInterface
    {
        return $this->container->get('validator');
    }

    /**
     * Proxy to Request methods
     *
     * @param $method
     * @param $arguments
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        return call_user_func_array([$this->request, $method], $arguments);
    }
}