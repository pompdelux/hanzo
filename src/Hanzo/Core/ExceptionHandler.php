<?php /* vim: set sw=4: */

/**
 * Handles 404 exceptions
 *
 * @see http://symfony.com/doc/current/book/internals.html#kernel-kernel-exception
 */

namespace Hanzo\Core;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Predis\Network\ConnectionException as PredisConnectionException;

use Symfony\Component\HttpFoundation\Response;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;

use Hanzo\Model\CmsI18n;
use Hanzo\Model\CmsI18nQuery;
use Hanzo\Model\Redirects;
use Hanzo\Model\RedirectsQuery;

class ExceptionHandler
{
    private $service_container;
    private $kernel;

    /**
     * __construct
     *
     * @param Object $service_container
     */
    public function __construct(ContainerInterface $service_container, HttpKernelInterface $kernel)
    {
        $this->service_container = $service_container;
        $this->kernel = $kernel;
    }

    /**
     * intercept onKernelException events
     *
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $request = $this->service_container->get('request');

        // 404 hangeling
        if ($exception instanceof NotFoundHttpException) {
            $path = $request->getPathInfo();
            $hanzo = Hanzo::getInstance();

            // attempt to fix images in old newsletters. Redirect to static
            if (substr($path, 6, 19) == '/images/nyhedsbrev/') {
                $newpath = substr($path, 6);
                $response = new Response('', 301, array('Location' => 'http://static.pompdelux.com'.$newpath));
                $event->setResponse($response);
            }

            // test for redirects
            $redirect = RedirectsQuery::create()
                ->filterByDomainKey($hanzo->get('core.domain_key'))
                ->findOneBySource($path)
            ;

            if ($redirect instanceof Redirects) {
                $url = $redirect->getTarget();
                if (substr($url, 0, 4) != 'http') {
                    $url = $request->getBaseUrl().''.$url;
                }

                $response = new Response('', 302, array('Location' => $url));
                $event->setResponse($response);
            }

            // Try to find a cms with the given path.
            // Maybe there have not been a cache:clear after CMS got published.
            $url = explode('/', $request->getPathInfo(), 3);

            if (!empty($url[2])) {

                // URL:  /da_DK/some/slug
                // SLUG: some/slug
                $slug = $url[2];

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


            // if webshop is offline, then set another message than "not found"
            if (0 == $hanzo->get('webshop.closed', 0)) {
                $twig = $this->service_container->get('twig');
                $twig->addGlobal('webshop_closed', true);
            }

        } elseif (
            ($exception instanceof RouteNotFoundException) ||
            ($exception instanceof ResourceNotFoundException) ||
            ($exception instanceof MethodNotAllowedException)
        ) {
            //Tools::log($exception->getMessage() . ' :: ' . $request->getPathInfo());

            $code = 404;
            if ($exception instanceof MethodNotAllowedException) {
                $code = 405;
            }

            $response = new Response($this->service_container->get('templating')->render('TwigBundle:Exception:error404.html.twig', array(
                'exception' => $exception
            )), $code);

            $event->setResponse($response);

        } elseif ($exception instanceof AccessDeniedHttpException) {
            $request = $this->service_container->get('request');

            switch (substr($request->getPathInfo(), 6)) {
                case '/account': // The customer probably tried to created an account on the wrong locale
                    $response = new Response('', 302, array('Location' => $request->getBaseUrl().'/'.$request->getLocale().'/login'));
                    $event->setResponse($response);
                    break;
            }

        } elseif ($exception instanceof PredisConnectionException) {
            Tools::log('Predis connection failed.');
            $event->setResponse(new Response('', 500));
        }
    }
}
