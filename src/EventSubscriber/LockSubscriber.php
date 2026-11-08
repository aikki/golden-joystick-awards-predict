<?php

namespace App\EventSubscriber;

use App\Repository\AwardRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

readonly class LockSubscriber implements EventSubscriberInterface
{
    public const string LOCKED_PATH = 'app_locked';

    public function __construct(
        private AwardRepository       $awardRepository,
        private UrlGeneratorInterface $urlGenerator,
    ) {}

    public function onRequestEvent(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $route = $request->attributes->get('_route');

        if (!$route || str_starts_with($route, '_') || $route === self::LOCKED_PATH) {
            return;
        }

        if ($this->awardRepository->count(['isDefault' => true]) === 1) {
            return;
        }

        $url = $this->urlGenerator->generate(self::LOCKED_PATH);

        $event->setResponse(new RedirectResponse($url));
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'onRequestEvent',
        ];
    }
}
