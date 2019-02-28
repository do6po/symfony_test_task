<?php
/**
 * Created by PhpStorm.
 * User: box
 * Date: 25.02.19
 * Time: 18:01
 */

namespace AppBundle\Requests;


use AppBundle\Entity\User;
use AppBundle\Interfaces\FillableFromRequestInterface;

class UserRequest extends AbstractRequest
{
    public function getFillableFromRequestObject(): FillableFromRequestInterface
    {
        return new User();
    }
}