<?php

namespace Hanzo\Bundle\EventsBundle\Controller;

use Hanzo\Core\CoreController;
use Hanzo\Model\CmsI18n;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DefaultController
 *
 * @package Hanzo\Bundle\EventsBundle
 */
class AdvisorController extends CoreController
{
    /**
     * @param CmsI18n $page
     *
     * @return Response
     *
     * @ParamConverter("page", class="Hanzo\Model\CmsI18n", options={"with"={"Cms"}})
     */
    public function renderAction(CmsI18n $page)
    {
        $cms = $page->getCms();

        // supported types:
        //   advisor_finder
        //   advisor_map
        //   advisor_open_house
        $tpl = $type = $cms->getType();
        $tpl = str_replace('advisor_', 'EventsBundle:Advisor:', $tpl) . '.html.twig';

        // supported $settings = [
        //   'show_all' => 1,
        //   'country'  => 'xxx'
        // ]
        $settings = (array) $page->getSettings(false);
        $settings['page_type'] = $type;

        return $this->render('CMSBundle:Default:view.html.twig', [
            'page_type'        => $type,
            'page'             => $page,
            'embedded_content' => $this->renderView($tpl, $settings),
            'parent_id'        => $cms->getParentId() ?: $page->getId(),
            'browser_title'    => $page->getTitle(),
        ]);
    }
}
