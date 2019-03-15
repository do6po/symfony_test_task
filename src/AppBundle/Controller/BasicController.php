<?php
/**
 * Created by PhpStorm.
 * User: box
 * Date: 04.03.19
 * Time: 13:29
 */

namespace AppBundle\Controller;


use AppBundle\Exceptions\RequestValidationErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Validator\ConstraintViolation;

class BasicController extends Controller
{
    /**
     * @param $object
     * @throws RequestValidationErrorException
     */
    protected function validate($object)
    {
        $constraintViolationList = $this->getValidator()
            ->validate($object);

        if ($constraintViolationList->count()) {
            $errors = [];
            foreach ($constraintViolationList as $constraintViolation) {
                /** @var ConstraintViolation $constraintViolation */
                $errors[$constraintViolation->getPropertyPath()][] = $constraintViolation->getMessage();
            }

            throw new RequestValidationErrorException($errors);
        }
    }

    /**
     * @return object|\Symfony\Component\Validator\Validator\TraceableValidator|\Symfony\Component\Validator\Validator\ValidatorInterface
     */
    protected function getValidator()
    {
        return $this->get('validator');
    }
}