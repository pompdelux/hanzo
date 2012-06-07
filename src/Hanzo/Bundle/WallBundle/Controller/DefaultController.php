<?php

namespace Hanzo\Bundle\WallBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
        return $this->render('WallBundle:Default:wall.html.twig', array(
            'page_type'     => 'wall'
        ));
    }

    public function getWallAction($pager)
    {
        $wall_posts = WallQuery::create()
            ->join('Customers')
            ->withColumn('CONCAT(customers.first_name, \' \', customers.last_name)', 'author')
            ->leftJoin('WallLikes')
            ->withColumn('wall_likes.status', 'liked')
            ->filterByStatus(true)
            ->where('wall.parent_id IS NULL')
            ->orderByCreatedAt('DESC')
            ->paginate($pager, 10)
        ;
        $posts = array();
        foreach ($wall_posts as $wall_post) {


            $likes = WallLikesQuery::create()
                ->join('Customers')
                ->withColumn('CONCAT(customers.first_name, \' \', customers.last_name)', 'name')
                ->filterByWallId($wall_post->getId())
                ->orderByStatus('DESC')
                ->find()
            ;

            $likes_arr = null;
            $total_likes = $total_dislikes = 0;
            foreach ($likes as $like) {

                // Count the number of likes/dislikes
                if($like->getStatus())
                    $total_likes++;
                else
                    $total_dislikes++;

                $likes_arr[] = array(
                    'id' => $like->getId(),
                    'customers_id' => $like->getCustomersId(),
                    'name' => $like->getName(),
                    'status' => $like->getStatus()
                );
            }

            $sub_posts = WallQuery::create()
                ->joinWith('Customers')
                ->withColumn('CONCAT(customers.first_name, \' \', customers.last_name)', 'author')
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
                    'created_at' => $sub_post->getCreatedAt(),
                    'author' => $sub_post->getAuthor(),
                    'customers_id' => $sub_post->getCustomersId(),
                    'is_author' => ($this->get('security.context')->getToken()->getUser()->getPrimaryKey() == $sub_post->getCustomersId()) ? true : false,
                    'is_first' => $sub_posts->isFirst(),
                    'is_last' => $sub_posts->isLast(),
                );
            }

            $posts[] = array(
                'id' => $wall_post->getId(),
                'message' => $wall_post->getMessate(),
                'created_at' => date('j. M Y - H:i', strtotime($wall_post->getCreatedAt())),
                'author' => $wall_post->getAuthor(),
                'customers_id' => $wall_post->getCustomersId(),
                'is_liked' => $wall_post->getLiked(),
                'is_author' => ($this->get('security.context')->getToken()->getUser()->getPrimaryKey() == $wall_post->getCustomersId()) ? true : false,
                'is_first' => $wall_posts->isFirst(),
                'is_last' => $wall_posts->isLast(),
                'num_likes' => ($total_likes > 1) ? $this->get('translator')->trans('wall.likes.plural.%likes%', array('%likes%' => $total_likes), 'wall') : $this->get('translator')->trans('wall.likes.single.%likes%', array('%likes%' => $total_likes), 'wall'),
                'num_dislikes' => ($total_dislikes > 1) ? $this->get('translator')->trans('wall.dislikes.plural.%dislikes%', array('%dislikes%' => $total_dislikes), 'wall') : $this->get('translator')->trans('wall.dislikes.single.%dislikes%', array('%dislikes%' => $total_dislikes), 'wall'),
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
        }else{
            return $this->json_response(array(
                'status' => true,
                'next_page' => ++$pager,
                //'number_of_posts' => $wall_posts->getLastIndex(),
                'data' => $posts
            ));
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
