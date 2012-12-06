<?php /* vim: set sw=4: */

/**
 * Handles 404 exceptions
 *
 * @see http://symfony.com/doc/current/book/internals.html#kernel-kernel-exception
 */

namespace Hanzo\Core;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\HttpFoundation\Response;

use Predis\Network\ConnectionException as PredisConnectionException;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;

use Hanzo\Model\CmsI18n;
use Hanzo\Model\CmsI18nQuery;
use Hanzo\Model\Redirects;
use Hanzo\Model\RedirectsQuery;

class ExceptionHandler
{
    private $service_container;

    /**
     * __construct
     *
     * @param Object $service_container
     */
    public function __construct($service_container)
    {
        $this->service_container = $service_container;
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

            // attempt to fix images in old newsletters. Redirect to static
            if (substr($path, 6, 19) == '/images/nyhedsbrev/') {
                $newpath = substr($path, 6);
                $response = new Response('', 301, array('Location' => 'http://static.pompdelux.com'.$newpath));
                $event->setResponse($response);
            }

            // try to map old shop ulr's to new ones
            if (substr($path, 6, 3) == '/p/') {
                $page = CmsI18nQuery::create()
                    ->filterByLocale($request->getLocale())
                    ->findOneByOldPath(substr($path, 6))
                ;
                if ($page instanceof CmsI18n) {
                    $response = new Response('', 301, array('Location' => $request->getBaseUrl().'/'.$request->getLocale().'/'.$page->getPath()));
                    $event->setResponse($response);
                }
            } else {
                // test for redirects
                $redirect = RedirectsQuery::create()
                    ->filterByDomainKey(Hanzo::getInstance()->get('core.domain_key'))
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
            }

        } elseif ($exception instanceof RouteNotFoundException) {
            Tools::log($exception->getMessage() . ' :: ' . $request->getPathInfo());

            $response = new Response($this->service_container->get('templating')->render('TwigBundle:Exception:error404.html.twig', array(
                'exception' => $exception
            )));

            $event->setResponse($response);

        } elseif ($exception instanceof AccessDeniedHttpException) {
            $request = $this->service_container->get('request');
            $pathWithNoLocale = substr($request->getPathInfo(),6);

            switch ( $pathWithNoLocale ) {
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
