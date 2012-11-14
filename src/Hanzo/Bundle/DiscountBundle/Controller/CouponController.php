<?php

namespace Hanzo\Bundle\DiscountBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;

use Criteria;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Core\CoreController;

use Hanzo\Model\Coupons;
use Hanzo\Model\CouponsQuery;
use Hanzo\Model\OrdersPeer;

class CouponController extends CoreController
{
    public function blockAction()
    {
        return $this->render('DiscountBundle:Coupon:block.html.twig');
    }

    public function applyCouponAction(Request $request)
    {
        $translator = $this->get('translator');

        $form = $this->createFormBuilder(new Coupons())
            ->add('code', 'text', array(
                'label'              => 'coupon.label',
                'error_bubbling'     => true,
                'translation_domain' => 'checkout',
            ))
            ->getForm()
        ;

        if ($request->getMethod() == 'POST') {
            $values = $request->get('form');
            // handle sub-requests
            $code = $values ?: $request->get('code');

            $coupon = CouponsQuery::create()
                ->filterByCode($code)
                ->filterByActiveFrom(time(), Criteria::GREATER_EQUAL)
                ->_or()
                ->filterByActiveFrom(null, Criteria::ISNULL)
                ->filterByActiveTo(time(), Criteria::LESS_EQUAL)
                ->_or()
                ->filterByActiveTo(null, Criteria::ISNULL)
                ->findOne()
            ;

            if (!$coupon instanceof Coupons) {
                $form->addError(new FormError('invalid.coupon.code'));

                if ($this->getFormat() == 'json') {
                    return $this->json_response(array(
                        'status'  => false,
                        'message' => $translator->trans('invalid.coupon.code', [], 'checkout'),
                    ));
                }

            } else {
                $order    = OrdersPeer::getCurrent();
                $total    = $order->getTotalPrice();
                $discount = $coupon->getAmount();

                if ($total < $discount) {
                    $discount = $total;
                    $coupon->setAmount($coupon->getAmount() - $total);
                    $coupon->save();

                    // change the payment method, you should not go through gothia/dibs/... if the total is 0.00
                } else {
                    $coupon->setAmount(0);
                }
                $coupon->save();

                $order->setDiscountLine($translator->trans('coupon', [], 'checkout'), -$discount, 'coupon.code');
                $order->setAttribute('amount', 'coupon', $discount);
                $order->setAttribute('code', 'coupon', $coupon->getCode());
                $order->save();

                if ($this->getFormat() == 'json') {
                    return $this->json_response(array(
                        'status'  => true,
                        'message' => ''
                    ));
                }

                return $this->redirect($this->generateUrl('_checkout'));
            }

            if ($this->getFormat() == 'json') {
                return $this->json_response(array(
                    'status'  => false,
                    'message' => ''
                ));
            }
        }

        return $this->render('DiscountBundle:Coupon:form.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
