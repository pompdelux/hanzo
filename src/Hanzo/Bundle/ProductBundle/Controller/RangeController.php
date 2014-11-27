<?php

namespace Hanzo\Bundle\ProductBundle\Controller;

use Hanzo\Core\CoreController;
use Symfony\Component\HttpFoundation\Request;

class RangeController extends CoreController
{
    /**
     * Override the active product range, only available as employees tho.
     *
     * @param  Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function setRangeAction(Request $request)
    {
        if (false === $this->get('security.context')->isGranted('ROLE_EMPLOYEE')) {
            return $this->redirect($this->generateUrl('_homepage'));
        }

        $this->container->get('hanzo_product.range')->setSessionOverride($request->query->get('range'));
        return $this->redirect($this->generateUrl('_homepage'));
    }
}
