<?php
/**
 * Created by PhpStorm.
 * User: box
 * Date: 25.02.19
 * Time: 19:20
 */

namespace AppBundle\Exceptions;


class RequestValidationErrorException extends BasicApiException
{
    protected $statusCode = 422;

    protected $message = 'Request validation error!';
}