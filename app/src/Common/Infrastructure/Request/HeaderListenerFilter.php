<?php
declare(strict_types=1);
namespace GridCP\Common\Infrastructure\Request;

use GridCP\Common\Domain\Exceptions\BadHeaderError;
use GridCP\Common\Domain\Exceptions\NotAuthorizedByHeader;
use GridCP\Common\Infrastructure\Jwt\UserServiceJwt;
use http\Exception\BadHeaderException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class HeaderListenerFilter implements EventSubscriberInterface
{
    private array $open_routes=[
        '/^\/api\/doc$/',
        '/^\/api\/v(0?[1-9]|[1-9][0-9])\/auth\/login$/',
        '/^\/api\/v(0?[1-9]|[1-9][0-9])\/auth\/register$/',
        '/^\/api\/v(0?[1-9]|[1-9][0-9])\/auth\/refreshToken$/',
        '/^\/api\/v(0?[1-9]|[1-9][0-9])\/auth\/restore\/password\/([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/',
        '/^\/api\/v(0?[1-9]|[1-9][0-9])\/auth\/restore\/password\/email\/[0-9a-f]{8}-[0-9a-f]{4}-[0-5][0-9a-f]{3}-[089ab][0-9a-f]{3}-[0-9a-f]{12}$/',
        '/^\/api\/v(0?[1-9]|[1-9][0-9])\/auth\/change\/password$/',
        '/^\/api\/v(0?[1-9]|[1-9][0-9])\/healthcheck$/',
        '/^\/api\/v(0?[1-9]|[1-9][0-9])\/configuration\/communication$/',
        '/^\/api\/v(0?[1-9]|[1-9][0-9])\/communications\/emails\/template$/',
        '/^\/api\/v(0?[1-9]|[1-9][0-9])\/communications\/emails\/templates\/[0-9a-f]{8}-[0-9a-f]{4}-[0-5][0-9a-f]{3}-[089ab][0-9a-f]{3}-[0-9a-f]{12}\/default$/',
        '/^\/api\/v(0?[1-9]|[1-9][0-9])\/device$/',
        '/^\/api\/v(0?[1-9]|[1-9][0-9])\/plan$/',
        '/^\/api\/v(0?[1-9]|[1-9][0-9])\/plan\/[0-9a-f]{8}-[0-9a-f]{4}-[0-5][0-9a-f]{3}-[089ab][0-9a-f]{3}-[0-9a-f]{12}$/',
        '/^\/api\/v(0?[1-9]|[1-9][0-9])\/client$/',
        '/^\/api\/v(0?[1-9]|[1-9][0-9])\/client\/[0-9a-f]{8}-[0-9a-f]{4}-[0-5][0-9a-f]{3}-[089ab][0-9a-f]{3}-[0-9a-f]{12}$/',
        '/^\/api\/v(0?[1-9]|[1-9][0-9])\/clients\/list$/',
        '/^\/api\/v(0?[1-9]|[1-9][0-9])\/clients\/user$/',
        '/^\/api\/v(0?[1-9]|[1-9][0-9])\/user\/me$/',
    ];




    public function __construct(private readonly UserServiceJwt $user){

    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        $uri = $request->getRequestUri();
        if ($this->isInOpenRoutes($uri)) return null;
        $headerValue = $request->headers->get('Gridcpclient');
        $roles = $this->user->getCurrentUser()->getRoles();
        $authClients = $this->user->getCurrentUser()->getAuthClients();
        $isAdmin = in_array('ROLE_ADMIN', $roles, true);
        if ($isAdmin) return null;
        if (!$headerValue && !$isAdmin) throw new BadHeaderError();
        if($this->findClient($headerValue, $authClients) || $isAdmin ) return null;
        return throw new NotAuthorizedByHeader();
    }


    private function isInOpenRoutes(string $uri): bool
    {
        foreach ($this->open_routes as $route) {
            if (preg_match($route, $uri, $matches)) {
                return true;
            }
        }
        return false;
    }

    private function findClient(string $client, array $authClients):bool{
         $result = false;
        foreach ($authClients as $authClient) {
            if ($authClient->getClientUuid() === $client)  $result = true;
        }
        return $result;
    }
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}