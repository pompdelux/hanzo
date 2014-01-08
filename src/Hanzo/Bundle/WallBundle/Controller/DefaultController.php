<?php

namespace Hanzo\Bundle\WallBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

use Hanzo\Core\Hanzo,
    Hanzo\Core\Tools,
    Hanzo\Core\CoreController;

use Hanzo\Model\Wall;
use Hanzo\Model\WallQuery;
use Hanzo\Model\WallLikes;
use Hanzo\Model\WallLikesQuery;

class DefaultController extends CoreController
{

    public function indexAction()
    {
        if ((false === $this->get('security.context')->isGranted('ROLE_ADMIN')) &&
            (false === $this->get('security.context')->isGranted('ROLE_CONSULTANT'))
        ) {
            return $this->redirect($this->generateUrl('login', ['_locale' => $this->get('request')->getLocale()]));
        }

        $locale = explode('_', Hanzo::getInstance()->get('core.locale'));
        $code = array_pop($locale);

        return $this->render('WallBundle:Default:wall.html.twig', array(
            'postfix' => $code,
            'page_type' => 'wall'
        ));
    }

    public function getWallAction($pager)
    {

        if ((false === $this->get('security.context')->isGranted('ROLE_ADMIN')) &&
            (false === $this->get('security.context')->isGranted('ROLE_CONSULTANT'))
        ) {
            throw new AccessDeniedException();
        }

        $wall_posts = WallQuery::create()
            ->joinWithCustomers()
            ->useCustomersQuery()
                ->useAddressesQuery()
                    ->filterByType('payment')
                    ->_or()
                    ->filterByType('shipping')
                    ->orderByType('ASC')
                ->endUse()
            ->endUse()
            ->groupById()
            ->filterByStatus(true)
            ->filterByParentId(NULL, \Criteria::ISNULL)
            ->orderByCreatedAt('DESC')
            ->paginate($pager, 10)
        ;

        $posts = array();
        foreach ($wall_posts as $wall_post) {

            $likes = WallLikesQuery::create()
                ->joinWithCustomers()
                ->filterByWallId($wall_post->getId())
                ->orderByStatus('DESC')
                ->find()
            ;

            $is_liked = WallLikesQuery::create()
                ->useCustomersQuery()
                    ->filterById($this->get('security.context')->getToken()->getUser()->getPrimaryKey())
                ->endUse()
                ->filterByWallId($wall_post->getId())
                ->findOne()
            ;

            $likes_arr = null;
            $total_likes = $total_dislikes = 0;
            foreach ($likes as $like) {

                // Count the number of likes/dislikes
                if ($like->getStatus()) {
                    $total_likes++;
                } else {
                    $total_dislikes++;
                }

                $likes_arr[] = array(
                    'id' => $like->getId(),
                    'customers_id' => $like->getCustomersId(),
                    'name' => $like->getCustomers()->getFirstName().' '.$like->getCustomers()->getLastName(),
                    'status' => $like->getStatus()
                );
            }

            $sub_posts = WallQuery::create()
                ->joinWithCustomers()
                ->useCustomersQuery()
                    ->useAddressesQuery()
                        ->filterByType('payment')
                        ->_or()
                        ->filterByType('shipping')
                        ->orderByType('ASC')
                    ->endUse()
                ->endUse()
                ->groupById()
                ->filterByStatus(true)
                ->filterByParentId($wall_post->getId())
                ->orderByCreatedAt('ASC')
                ->find()
            ;

            $sub_posts_arr = null;
            foreach ($sub_posts as $sub_post) {
                $sub_posts_arr[] = array(
                    'id' => $sub_post->getId(),
                    'message' => $this->wallEmo($sub_post->getMessate()),
                    'clean_message' => $sub_post->getMessate(),
                    'created_at' => date('j. M Y - H:i', strtotime($sub_post->getCreatedAt())),
                    'author' => $sub_post->getCustomers()->getFirstName().' '.$sub_post->getCustomers()->getLastName(),
                    'city' => $sub_post->getCustomers()->getAddresses()->getFirst()->getCity(),
                    'customers_id' => $sub_post->getCustomersId(),
                    'is_author' => ($this->get('security.context')->getToken()->getUser()->getPrimaryKey() == $sub_post->getCustomersId()) ? true : false,
                    'is_first' => $sub_posts->isFirst(),
                    'is_last' => $sub_posts->isLast(),
                );
            }

            $posts[] = array(
                'id' => $wall_post->getId(),
                'message' => $this->wallEmo($wall_post->getMessate()),
                'clean_message' => $wall_post->getMessate(),
                'created_at' => date('j. M Y - H:i', strtotime($wall_post->getCreatedAt())),
                'author' => $wall_post->getCustomers()->getFirstName().' '.$wall_post->getCustomers()->getLastName(),
                'city' => $wall_post->getCustomers()->getAddresses()->getFirst()->getCity(),
                'customers_id' => $wall_post->getCustomersId(),
                'is_liked' => ($is_liked instanceof WallLikes) ? $is_liked->getStatus() : null,
                'is_author' => ($this->get('security.context')->getToken()->getUser()->getPrimaryKey() == $wall_post->getCustomersId()) ? true : false,
                'is_first' => $wall_posts->isFirst(),
                'is_last' => $wall_posts->isLast(),
                'num_likes' => $total_likes,
                'num_dislikes' => $total_dislikes,
                'likes' => $likes_arr,
                'number_of_subposts' => $sub_posts->count(),
                'sub_posts' => $sub_posts_arr
            );


        }
        //print_r(get_class_methods($wall_posts));
        if ($this->getFormat() == 'json') {
            return $this->json_response(array(
                'status' => true,
                'next_page' => ++$pager,
                //'number_of_posts' => $wall_posts->getLastIndex(),
                'data' => $posts
            ));
        } else {
            return $this->json_response(array(
                'status' => true,
                'next_page' => ++$pager,
                //'number_of_posts' => $wall_posts->getLastIndex(),
                'data' => $posts
            ));
        }
    }

    public function editEntryAction($id)
    {
        if ((false === $this->get('security.context')->isGranted('ROLE_ADMIN')) &&
            (false === $this->get('security.context')->isGranted('ROLE_CONSULTANT'))
        ) {
            throw new AccessDeniedException();
        }

        $wall_entry = WallQuery::create()->findOneById($id);

        if ($wall_entry instanceof Wall) {
            $request = $this->get('request');
            if ('POST' === $request->getMethod()) {

                $wall_entry->setMessate($request->request->get('message')); // messate = message :-)
                $wall_entry->save();

                if ($this->getFormat() == 'json') {
                    return $this->json_response(array(
                        'status' => true,
                        'message' => $this->get('translator')->trans('wall.edit.entry.success', array(), 'wall'),
                        'input' => $request->request->get('message'),
                        'id' => $id
                    ));
                }
            }
        } else {
            if ($this->getFormat() == 'json') {
                return $this->json_response(array(
                    'status' => false,
                    'message' => $this->get('translator')->trans('wall.edit.entry.failed', array(), 'wall')
                ));
            }
        }
    }

    /**
     * @param Request $request
     * @param int     $id the id of the parent entry(comment). Default null(new entry)
     * @throws AccessDeniedException
     * @return Response
     */
    public function addEntryAction(Request $request, $id)
    {

        if ((false === $this->get('security.context')->isGranted('ROLE_ADMIN')) &&
            (false === $this->get('security.context')->isGranted('ROLE_CONSULTANT'))
        ) {
            throw new AccessDeniedException();
        }

        if ('POST' === $request->getMethod()) {
            $creator = $this->get('security.context')->getToken()->getUser();

            $wall_entry = new Wall();

            if ($id) { // Its a comment to a parent entry
                $wall_entry->setParentId($id);
            }

            $wall_entry->setCustomersId($creator->getPrimaryKey());
            $wall_entry->setMessate($request->request->get('message')); // messate = message :-)
            $wall_entry->setStatus(true);
            $wall_entry->save();

            if ($id) { // If its a comment to another post, inform all participants of the entry by mail.
                $users = WallQuery::create()
                    ->useCustomersQuery()
                        ->groupByEmail()
                    ->endUse()
                    ->joinWithCustomers()
                    ->filterById($id)
                    ->_or()
                    ->filterByParentId($id)
                    ->find()
                ;

                foreach ($users as $user) {
                    $customer = $user->getCustomers();
                    $author = $creator->getUser();

                    $mailer = $this->get('mail_manager');
                    $mailer->setMessage('wall.reply', array(
                        'name' => $author->getFirstName().' '.$author->getLastName(),
                        'to_name' => $customer->getFirstName(),
                        'first_name' => $author->getFirstName(),
                        'comment' => $wall_entry->getMessate(),
                    ));

                    try {
                        $mailer->setTo($customer->getEmail(), $customer->getFirstName());
                        $mailer->send();
                    } catch (Exception $e) {
                        Tools::log($e);
                    }
                }
            }

            $wall_post = WallQuery::create()
                ->join('Customers')
                ->findOneById($wall_entry->getId())
            ;

            $post = null;

            if ($id) {
                $post[] = array(
                    'parent_id' => $id,
                    'id' => $wall_post->getId(),
                    'message' => $this->wallEmo($wall_post->getMessate()),
                    'clean_message' => $wall_post->getMessate(),
                    'created_at' => date('j. M Y - H:i', strtotime($wall_post->getCreatedAt())),
                    'author' => $wall_post->getCustomers()->getFirstName().' '.$wall_post->getCustomers()->getLastName(),
                    'customers_id' => $wall_post->getCustomersId(),
                    'is_liked' => false,
                    'is_author' => ($creator->getPrimaryKey() == $wall_post->getCustomersId()) ? true : false,
                    'is_first' => false,
                    'is_last' => false,
                    'num_likes' => 0,
                    'num_dislikes' => 0,
                    'likes' => null,
                    'number_of_subposts' => 0,
                    'sub_posts' => null
                );
            } else {
                $post[] = array(
                    'id' => $wall_post->getId(),
                    'message' => $this->wallEmo($wall_post->getMessate()),
                    'clean_message' => $wall_post->getMessate(),
                    'created_at' => date('j. M Y - H:i', strtotime($wall_post->getCreatedAt())),
                    'author' => $wall_post->getCustomers()->getFirstName().' '.$wall_post->getCustomers()->getLastName(),
                    'customers_id' => $wall_post->getCustomersId(),
                    'is_author' => ($creator->getPrimaryKey() == $wall_post->getCustomersId()) ? true : false,
                    'is_first' => false,
                    'is_last' => false,
                );
            }

            if ($this->getFormat() == 'json') {
                return $this->json_response(array(
                    'status' => true,
                    'message' => $id ? $this->get('translator')->trans('wall.add.comment.success', array(), 'wall') : $this->get('translator')->trans('wall.add.entry.success', array(), 'wall'),
                    'data' => $post,

                ));
            }
        }
    }
    /**
     * @param id the id of the story to like/dislike
     * @param status the status of the action. Like=1, dislike=0
     */
    public function likeEntryAction($id, $status)
    {
        if ((false === $this->get('security.context')->isGranted('ROLE_ADMIN')) &&
            (false === $this->get('security.context')->isGranted('ROLE_CONSULTANT'))
        ) {
            throw new AccessDeniedException();
        }

        $is_liked = WallLikesQuery::create()
            ->filterByWallId($id)
            ->findOneByCustomersId($this->get('security.context')->getToken()->getUser()->getPrimaryKey())
        ;

        if ($is_liked instanceof WallLikes) {

            if ($is_liked->getStatus() == $status) {
                // If the status are the same, its a toggle. Then delete it.

                $is_liked->delete();

                if ($this->getFormat() == 'json') {
                    return $this->json_response(array(
                        'status' => true,
                        'message' => $this->get('translator')->trans('wall.entry.like.remove', array(), 'wall')
                    ));
                }

            } else {
                // If they are not the same, its a change from like <> dislike.
                $is_liked->setStatus($status);
                $is_liked->save();

                if ($this->getFormat() == 'json') {
                    return $this->json_response(array(
                        'status' => true,
                        'message' => $this->get('translator')->trans('wall.entry.like.toggle', array(), 'wall')
                    ));
                }
            }


        } else {
            $is_liked = new WallLikes();
            $is_liked->setWallId($id);
            $is_liked->setCustomersId($this->get('security.context')->getToken()->getUser()->getPrimaryKey());
            $is_liked->setStatus($status);
            $is_liked->save();

            if ($this->getFormat() == 'json') {
                return $this->json_response(array(
                    'status' => true,
                    'message' => $this->get('translator')->trans('wall.entry.like.add', array(), 'wall')
                ));
            }
        }

    }

    public function deleteEntryAction($id)
    {
        if ((false === $this->get('security.context')->isGranted('ROLE_ADMIN')) &&
            (false === $this->get('security.context')->isGranted('ROLE_CONSULTANT'))
        ) {
            throw new AccessDeniedException();
        }

        $wall_entry = WallQuery::create();

        if ((false == $this->get('security.context')->isGranted('ROLE_ADMIN')) &&
            (false == $this->get('security.context')->isGranted('ROLE_SALES'))
        ) {
            $wall_entry = $wall_entry->filterByCustomersId($this->get('security.context')->getToken()->getUser()->getPrimaryKey());
        }

        $wall_entry = $wall_entry->findOneById($id);

        if ($wall_entry instanceof Wall) {
            $wall_entry->delete();
            if ($this->getFormat() == 'json') {
                return $this->json_response(array(
                    'status' => true,
                    'message' => $this->get('translator')->trans('wall.entry.delete.success', array(), 'wall')
                ));
            }
        } else {
            if ($this->getFormat() == 'json') {
                return $this->json_response(array(
                    'status' => false,
                    'message' => $this->get('translator')->trans('wall.entry.delete.failed', array(), 'wall')
                ));
            }
        }
    }

    private function wallEmo($text) {
        $text = nl2br(htmlspecialchars($text));

        $find = array(
            ':)', ':-)', ':o)', // emo-7
            ':D', ':-D', ':d', ':-d', ':-))', // emo-1
            '&gt;((', '&gt;-((', ':-((', // emo-11
            ':(', ':-(', ':o(', // emo-2
            ';*(', ":'(", // emo-8
            ';)', ';-))', ';o)', ';-)', // emo-6
            ':o', ':-o', ':O', ':-O', // emo-9
            '8)', '8-)', '(l)', // emo-3
            ':@', '&gt;(', '&gt;-(', // emo-10
            ':p', ':-p', ':P', ':-P', // emo-12
        );

        $replace = array(
            '<em class="emo-7"> </em>', '<em class="emo-7"> </em>', '<em class="emo-7"> </em>',
            '<em class="emo-1"> </em>', '<em class="emo-1"> </em>', '<em class="emo-1"> </em>', '<em class="emo-1"> </em>', '<em class="emo-1"> </em>',
            '<em class="emo-11"> </em>', '<em class="emo-11"> </em>', '<em class="emo-11"> </em>',
            '<em class="emo-2"> </em>', '<em class="emo-2"> </em>', '<em class="emo-2"> </em>',
            '<em class="emo-8"> </em>', '<em class="emo-8"> </em>',
            '<em class="emo-6"> </em>', '<em class="emo-6"> </em>', '<em class="emo-6"> </em>', '<em class="emo-6"> </em>',
            '<em class="emo-9"> </em>', '<em class="emo-9"> </em>', '<em class="emo-9"> </em>', '<em class="emo-9"> </em>',
            '<em class="emo-3"> </em>', '<em class="emo-3"> </em>', '<em class="emo-3"> </em>',
            '<em class="emo-10"> </em>', '<em class="emo-10"> </em>', '<em class="emo-10"> </em>',
            '<em class="emo-12"> </em>', '<em class="emo-12"> </em>', '<em class="emo-12"> </em>', '<em class="emo-12"> </em>',
        );

        return str_replace($find, $replace, $text);
    }
}
