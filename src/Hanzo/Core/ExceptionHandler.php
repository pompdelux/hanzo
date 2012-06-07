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

use Hanzo\Model\CmsI18n;
use Hanzo\Model\CmsI18nQuery;

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

            // try to map old shop ulr's to new ones
            if (substr($request->getPathInfo(), 0, 3) == '/p/') {
                $page = CmsI18nQuery::create()
                    ->filterByLocale($this->service_container->get('session')->getLocale())
                    ->findOneByOldPath($request->getPathInfo())
                ;
                if ($page instanceof CmsI18n) {
                    $response = new Response('', 301, array('Location' => $request->getBaseUrl().'/'.$page->getPath()));
                    $event->setResponse($response);
                }
            }
        }
    }

}
