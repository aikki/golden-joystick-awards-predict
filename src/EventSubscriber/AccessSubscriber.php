<?php

namespace App\EventSubscriber;

use App\Repository\AwardRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

readonly class AccessSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private AwardRepository       $awardRepository,
    ) {}

    public function onRequestEvent(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $route = $request->attributes->get('_route');

        if (!$route || !str_starts_with($route, 'app_admin')) {
            return;
        }

        $award = $this->awardRepository->findDefault();

        if (!$award) {
            return;
        }

        $cookie = $request->cookies->get('dashboard_access');

        if (!$cookie || $cookie !== $award->getAdminKey()) {
            throw new NotFoundHttpException();
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'onRequestEvent',
        ];
    }
}
