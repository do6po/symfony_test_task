<?php
/**
 * Created by PhpStorm.
 * User: box
 * Date: 25.02.19
 * Time: 18:01
 */

namespace AppBundle\Requests;


use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserRequest extends AbstractRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                new Length(['min' => 3]),
                new NotBlank(),
            ],
            'email' => [
                new Email(),
                new NotBlank(),
            ]
        ];
    }
}