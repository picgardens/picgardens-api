<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class RequestCacheEventSubscriber implements EventSubscriberInterface
{
    private const DATABASE = 'instagram_new';
    private const COLLECTION = 'cache';

    private $isCache = false;

    /** @var \MongoClient */
    private $mongo;

    /**
     * RequestSubscriber constructor.
     * @param \MongoClient $mongo
     */
    public function __construct(\MongoClient $mongo)
    {
        $this->mongo = $mongo;
    }


    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        $credentials = [
            'path' => $request->getRequestUri()
        ];

        try {
            $result = $this->mongo->{self::DATABASE}->{self::COLLECTION}->findOne($credentials);
        } catch (\Exception $exception) {
            $result = null;
        }

        if ($result) {
            $this->isCache = true;

            $event->setResponse(
                new JsonResponse(json_decode($result['response'], true), 200)
            );

            return $event;
        }
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        if ($this->isCache) {
            return $event;
        }


        $request = $event->getRequest();
        $response = $event->getResponse();

        if(!$response instanceof JsonResponse) {
            return $event;
        }

        $data = [
            'path' => $request->getRequestUri(),
            'response' => $response->getContent(),
            'created_at' => new \MongoDate()
        ];

        try {
            $this->mongo->{self::DATABASE}->{self::COLLECTION}->insert($data);
        } catch (\Exception $exception) {

        }
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.request' => 'onKernelRequest',
            'kernel.response' => 'onKernelResponse',
        ];
    }
}
