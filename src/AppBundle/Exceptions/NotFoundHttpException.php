<?php
/**
 * Created by PhpStorm.
 * User: box
 * Date: 28.02.19
 * Time: 18:41
 */

namespace AppBundle\Exceptions;


class NotFoundHttpException extends BasicApiException
{
    protected $statusCode = 404;

    protected $message = 'Page not found!';
}