<?php
namespace Hanzo\Bundle\CMSBundle\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;

use Hanzo\Model\CmsI18nQuery;
use Hanzo\Model\CmsI18n;

use Hanzo\Core\Hanzo;

class ExceptionListener
{
    protected $container;
    protected $kernel;

    public function __construct(HttpKernelInterface $kernel, ContainerInterface $container)
    {
        $this->container = $container;
        $this->kernel = $kernel;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {

        if (!$this->container->isScopeActive('request')) {
            // Skip this, if we are not in a request scope.
            return;
        }

        $exception = $event->getException();

        // Catch all 404 exceptions. Try to find a cms with the given path.
        // Maybe there have not been a cache:clear after CMS got published.
        if (method_exists($exception, 'getStatusCode') && 404 == $exception->getStatusCode()) {
            $request = $this->container->get('request');

            $url = explode('/', $request->getPathInfo(), 3);

            if (!empty($url[2])) {

                // URL:  /da_DK/some/slug
                // SLUG: some/slug
                $slug = $url[2];

                $hanzo = Hanzo::getInstance();

                $cms = CmsI18nQuery::create()
                    ->filterByLocale($hanzo->get('core.locale'))
                    ->findOneByPath(trim($slug, '/'));

                if ($cms instanceof CmsI18n) {
                    // Create a sub request. This is needed to forward a call to
                    // a new controller from a service.
                    $subRequest = $request->duplicate(array(), null, array(
                        'id'  => $cms->getId(),
                        '_controller' => 'CMSBundle:Default:view',
                    ));

                    $response = $this->kernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
                    $event->setResponse($response);
                }
            }
        }
    }
}
