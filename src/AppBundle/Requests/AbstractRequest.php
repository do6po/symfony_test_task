<?php
/**
 * Created by PhpStorm.
 * User: box
 * Date: 25.02.19
 * Time: 19:14
 */

namespace AppBundle\Requests;


use AppBundle\Exceptions\RequestValidationErrorException;
use http\Env\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validation;
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
     * AbstractRequest constructor.
     *
     * @param RequestStack $requestStack
     * @throws RequestValidationErrorException
     */
    public function __construct(RequestStack $requestStack)
    {
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

    abstract public function rules(): array;

    public function validate()
    {
        $validator = $this->getValidator();

        foreach ($this->rules() as $attribute => $constraints) {
            $violations = $validator->validate(
                $this->request->get($attribute),
                $constraints
            );

            if ($violations->count()) {
                $this->pushToErrors($attribute, $this->extractErrors($violations));
            }
        }
    }

    public function pushToErrors(string $attribute, array $violationList)
    {
        $this->errors[$attribute] = $violationList;
    }

    public function extractErrors(ConstraintViolationListInterface $violationList)
    {
        $errors = [];

        foreach ($violationList as $item) {
            $errors[] = $item->getMessage();
        }

        return $errors;
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
        return Validation::createValidator();
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