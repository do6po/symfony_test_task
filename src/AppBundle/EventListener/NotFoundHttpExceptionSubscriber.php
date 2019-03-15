<?php
/**
 * Created by PhpStorm.
 * User: box
 * Date: 26.02.19
 * Time: 12:17
 */

namespace AppBundle\EventListener;


use AppBundle\Exceptions\BasicApiException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class NotFoundHttpExceptionSubscriber implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if (!$exception instanceof NotFoundHttpException) {
            return;
        }

        $response = new JsonResponse(
            [
                'message' => 'Not Found!'
            ],
            $exception->getStatusCode()
        );

        $response->headers->set('Content-Type', 'application/problem+json');
        $event->setResponse($response);

    }
}