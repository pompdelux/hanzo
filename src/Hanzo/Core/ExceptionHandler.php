<?php /* vim: set sw=4: */

/**
 * Handles 404 exceptions
 *
 * @see http://symfony.com/doc/current/book/internals.html#kernel-kernel-exception
 */

namespace Hanzo\Core;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;

use Hanzo\Core\Hanzo;

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

        // 404 hangeling
        if ($exception instanceof NotFoundHttpException) {
            $request = $this->service_container->get('request');

            $path = $request->getPathInfo();

            // try to map old shop ulr's to new ones
            if (substr($path, 0, 3) == '/p/') {
                $page = CmsI18nQuery::create()
                    ->filterByLocale($this->service_container->get('session')->getLocale())
                    ->findOneByOldPath($path)
                ;
                if ($page instanceof CmsI18n) {
                    $response = new Response('', 301, array('Location' => $request->getBaseUrl().'/'.$page->getPath()));
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
        }
    }

}
