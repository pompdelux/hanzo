<?php

namespace Hanzo\Bundle\DiscountBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;

use Criteria;

use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;
use Hanzo\Core\CoreController;

use Hanzo\Model\GiftCards;
use Hanzo\Model\GiftCardsQuery;
use Hanzo\Model\OrdersPeer;
use Hanzo\Model\OrdersToGiftCards;

class GiftCardController extends CoreController
{
    public function blockAction()
    {
        return $this->render('DiscountBundle:GiftCard:block.html.twig');
    }

    public function applyGiftCardAction(Request $request)
    {
        $translator = $this->get('translator');

        $form = $this->createFormBuilder(new GiftCards())
            ->add('code', 'text', [
                'label'              => 'gift_card.label',
                'error_bubbling'     => true,
                'translation_domain' => 'checkout',
            ])
            ->getForm()
        ;

        if ($request->getMethod() == 'POST') {
            $values = $request->request->get('form');
            // handle sub-requests
            $code = $values ?: $request->request->get('code');

            $gift_card = GiftCardsQuery::create()
                ->filterByCode($code)
                ->filterByAmount(0, Criteria::GREATER_THAN)
                ->filterByActiveFrom(time(), Criteria::LESS_EQUAL)
                ->_or()
                ->filterByActiveFrom(null, Criteria::ISNULL)
                ->filterByActiveTo(time(), Criteria::GREATER_EQUAL)
                ->_or()
                ->filterByActiveTo(null, Criteria::ISNULL)
                ->findOne()
            ;

            if (!$gift_card instanceof GiftCards) {
                $form->addError(new FormError('invalid.gift_card.code'));

                if ($this->getFormat() == 'json') {
                    return $this->json_response(array(
                        'status'  => false,
                        'message' => $translator->trans('invalid.gift_card.code', [], 'checkout'),
                    ));
                }

            } else {
                $order    = OrdersPeer::getCurrent();
                $total    = $order->getTotalPrice();
                $discount = $gift_card->getAmount();

                if ($total < $discount) {
                    $discount = $total;
                    $gift_card->setAmount($gift_card->getAmount() - $total);

                    // change the payment method, you should not go through gothia/dibs/... if the total is 0.00
                    $order->setPaymentMethod('gift_card');
                    $order->setPaymentPaytype('gift_card');
                } else {
                    $gift_card->setAmount(0);
                }

                if (0 == $gift_card->getAmount()) {
                    $gift_card->setIsActive(false);
                }

                $gift_card->save();

                $relation = new OrdersToGiftCards();
                $relation->setOrdersId($order->getId());
                $relation->setGiftCardsId($gift_card->getId());
                $relation->setAmount($discount);
                $relation->save();

                $text = $translator->trans('gift_card', [], 'checkout');
                $order->setDiscountLine($text, -$discount, 'gift_card.code');
                $order->setAttribute('amount', 'gift_card', $discount);
                $order->setAttribute('code', 'gift_card', $gift_card->getCode());
                $order->setAttribute('text', 'gift_card', $text);
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

        return $this->render('DiscountBundle:GiftCard:form.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
