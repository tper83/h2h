<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class RequestSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        //Check For API POST
        if ($request->getRequestUri() === '/api/' && $request->getMethod() === 'POST' && $request->getContentTypeFormat() !== 'json') {
            throw new AccessDeniedHttpException('Payload needs to be json.');
        }

    }
}
