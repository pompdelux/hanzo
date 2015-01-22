<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\NewsletterBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Hanzo\Core\Hanzo;
use Hanzo\Core\CoreController;
use Hanzo\Model\CmsPeer;
use Hanzo\Model\CustomersPeer;

class DefaultController extends CoreController
{
    /**
     * handleAction
     *
     * @param string $action
     *
     * @return Response
     */
    public function handleAction( $action )
    {
        $name  = $this->get('request')->get('name');
        $email = $this->get('request')->get('email');
        $api   = $this->get('newsletterapi');

        $api->sendNotificationEmail( $action, $email, $name );
        return $this->json_response( array('error' => false) );
    }

    /**
     * jsAction
     * @return Response
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function jsAction()
    {
        $customer = CustomersPeer::getCurrent();
        $api      = $this->get('newsletterapi');
        $listId   = $api->getListIdAvaliableForDomain();

        return $this->render('NewsletterBundle:Default:js.html.twig', array(
            'customer' => $customer,
            'listid' => $listId,
            // Url is also hardcoded in NewsletterApi.php and in events.js
            'newsletter_jsonp_url' => 'http://phplist.pompdelux.dk/integration/json.php?callback=?'
        )
    );
    }

    /**
     * blockAction
     * @return Response
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function blockAction()
    {
        $customer = CustomersPeer::getCurrent();
        $api      = $this->get('newsletterapi');
        $listId   = $api->getListIdAvaliableForDomain();

        return $this->render('NewsletterBundle:Default:block.html.twig', array(
            'customer' => $customer,
            'listid'   => $listId,
            )
        );
    }

    /**
     * viewAction
     * @return Response
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function viewAction($id)
    {
        $page = CmsPeer::getByPK($id, Hanzo::getInstance()->get('core.locale'));

        if (is_null($page)) {
            throw $this->createNotFoundException('The page does not exist (id: '.$id.' )');
        }

        return $this->render('NewsletterBundle:Default:view.html.twig', array( 'page' => $page, 'page_type' => 'newsletter'));
    }


    /**
     * get a full list of available newsletters
     *
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function allListsAction(Request $request)
    {
        $customer = CustomersPeer::getCurrent();
        $api      = $this->get('newsletterapi');

        if ('POST' === $request->getMethod()) {
            $lists = $request->request->get('lists');
            if (empty($lists)) {
                $api->unsubscribe($customer->getEmail(), 'ALL');
            } else {
                $api->subscribe($customer->getEmail(), $lists);
            }
            return $this->redirect($this->generateUrl('_account'));
        }

        $lists = $api->getAllLists($customer->getEmail());

        return $this->render('NewsletterBundle:Default:account_form.html.twig', [
            'lists' => (isset($lists->content->lists) ? $lists->content->lists : []),
        ]);
    }


    /**
     * subscribe a user to a newsletter, will take existing subscriptions into account.
     *
     * @param  Request $request
     * @return Response
     */
    public function subscribeAction(Request $request)
    {
        if ('POST' !== $request->getMethod()) {
            return $this->redirect($this->generateUrl('_homepage'));
        }

        $email = $request->request->get('email');
        $name  = $request->request->get('name');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->json_response([
                'status'  => false,
                'message' => $this->get('translator')->trans('invalid.email.address', [], 'newsletter'),
            ]);
        }

        $api    = $this->get('newsletterapi');
        $id     = $api->getListIdAvaliableForDomain();
        $lists  = $api->getAllLists($email);
        $active = [$id => $id];

        // handle existing subscriptions
        if (isset($lists->content->lists)) {
            foreach ($lists->content->lists as $id => $list) {
                if ($list->is_subscribed) {
                    $active[$id] = $id;
                }
            }
        }

        $extraData = ['name' => $name];

        $result = $api->subscribe($email, $active, $extraData);
        $api->sendNotificationEmail('subscribe', $email, $name);

        return $this->json_response([
            'status'  => true,
            'message' => $this->get('translator')->trans('subscribed.text', [], 'newsletter'),
        ]);
    }

    public function unsubscribeAction(Request $request)
    {
        if ('POST' !== $request->getMethod()) {
            return $this->redirect($this->generateUrl('_homepage'));
        }

        $email = $request->request->get('email');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->json_response([
                'status'  => false,
                'message' => $this->get('translator')->trans('invalid.email.address', [], 'newsletter'),
            ]);
        }

        $api    = $this->get('newsletterapi');
        $id     = $api->getListIdAvaliableForDomain();
        $active = [$id => $id];
        $result = $api->unsubscribe($email, $active);
        $api->sendNotificationEmail('unsubscribe', $email);

        return $this->json_response([
            'status'  => true,
            'message' => $this->get('translator')->trans('unsubscribed.text', [], 'newsletter'),
        ]);
    }
}
