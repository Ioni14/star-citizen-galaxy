<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class RateLimitSubscriber implements EventSubscriberInterface
{
    // REQUEST_LIMIT_PER_ROUTE over EXPIRATION seconds
    private const EXPIRATION = 60;
    private const REQUEST_LIMIT_PER_ROUTE = 300;

    private \Redis $redis;

    public function __construct(\Redis $redis)
    {
        $this->redis = $redis;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onRequest', 31],
            KernelEvents::RESPONSE => ['onResponse', 10],
        ];
    }

    public function onRequest(RequestEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();

        $route = $request->attributes->get('_route');
        if (!$this->hasQuotas($route)) {
            return;
        }

        $clientIp = $request->getClientIp();

        $key = sprintf('route:%s;ip:%s', $route, $clientIp);
        $this->redis->incr($key);
        $quota = $this->redis->get($key);
        if ($quota <= 1) {
            $this->redis->expire($key, self::EXPIRATION);
        }
        if ($quota > self::REQUEST_LIMIT_PER_ROUTE) {
            throw new HttpException(429, 'Quota limits reached for this route.');
        }
        $request->attributes->set('api_key', $key);
        $request->attributes->set('api_period', self::EXPIRATION);
        $request->attributes->set('api_limit', self::REQUEST_LIMIT_PER_ROUTE);
    }

    public function onResponse(ResponseEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        $response = $event->getResponse();

        $key = $request->attributes->get('api_key');

        if ($response->getStatusCode() === 429) {
            return;
        }
        $response->headers->set('X-RateLimit-Limit', $limit = $request->attributes->get('api_limit'));
        $response->headers->set('X-RateLimit-Remaining', $limit - $this->redis->get($key));
        $response->headers->set('X-RateLimit-Reset', time() + $this->redis->ttl($key));
    }

    private function hasQuotas(string $route): bool
    {
        return stripos($route, 'api_') === 0;
    }
}
