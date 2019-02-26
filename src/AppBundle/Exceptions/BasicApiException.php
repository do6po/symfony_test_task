<?php
/**
 * Created by PhpStorm.
 * User: box
 * Date: 25.02.19
 * Time: 19:25
 */

namespace AppBundle\Exceptions;


class BasicApiException extends \Exception
{
    protected $messages = [];

    protected $statusCode = 0;

    protected $message = '';

    public function __construct(array $messages, string $message = "")
    {
        parent::__construct($this->message);

        $this->messages = $messages;
    }

    public function getMessages(): array
    {
        return $this->messages;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}