<?php

namespace Hanzo\Bundle\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Locale\Locale;

use Hanzo\Core\Hanzo,
    Hanzo\Core\Tools,
    Hanzo\Core\CoreController;

use Hanzo\Model\Cms,
    Hanzo\Model\CmsPeer,
    Hanzo\Model\CmsQuery;

use Hanzo\Bundle\AdminBundle\Form\Type\CmsType;
use Hanzo\Bundle\AdminBundle\Entity\CmsNode;
class CmsController extends CoreController
{

    public function indexAction()
    {
        return $this->render('AdminBundle:Cms:menu.html.twig',array('tree'=>$this->getCmsTree()));
    }

    public function deleteAction($node_id)
    {
        CmsQuery::create()
          ->findOneById($node_id)
          ->delete();
          
        if ($this->getFormat() == 'json') {
            return $this->json_response(array(
                'status' => TRUE,
                'message' => $this->get('translator')->trans('delete.node.success', array(), 'admin'),
            ));
        }
    }
    public function addAction($locale = 'en_EN')
    {
        $cms_node = new CmsNode();
        
        $form = $this->createFormBuilder($cms_node)
            ->add('type', 'choice', array(
                  'label'     => 'cms.edit.label.settings',
                  'choices'   => array(
                    'category'  => 'cms.edit.type.category',
                    'category_search'  => 'cms.edit.type.category_search',
                    'newsletter'  => 'cms.edit.type.newsletter',
                    'advanced_search'  => 'cms.edit.type.advanced_search',
                    'mannequin'  => 'cms.edit.type.mannequin'
                  ),
                  'required'  => FALSE,
                  'translation_domain' => 'admin'
              ))
            ->getForm();

        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $node = new Cms();
                $settings = array();
                switch ($cms_node->getType()) {
                    case 'category':
                        $node->setType('category');
                        $settings['type'] = 'category';
                        // Noget med category_id
                        break;
                    case 'category_search':
                        $node->setType('system');
                        $settings['type'] = 'category_search';
                        $settings['param']['categories'] = ''; //Dummy Data? 
                        $settings['param']['group'] = ''; //Dummy Data?
                        break;
                    case 'newsletter':
                        $node->setType('system');
                        $settings['type'] = 'newsletter';
                        break;
                    case 'advanced_search':
                        $node->setType('system');
                        $settings['type'] = 'advanced_search';
                        break;
                    case 'mannequin':
                        $node->setType('system');
                        $settings['type'] = 'mannequin';
                        $settings['param']['categories'] = ''; //Dummy Data? 
                        $settings['param']['image'] = ''; //Dummy Data? 
                        $settings['param']['title'] = ''; //Dummy Data?
                        $settings['param']['colorsheme'] = ''; //Dummy Data?
                        $settings['param']['ignore'] = ''; //Dummy Data?
                        break;
                    default:
                        $node->setType($cms_node->getType());
                        break;
                }

                $node->setSettings(json_encode($settings));
                $node->save();
                
                $this->get('session')->setFlash('notice', 'cms.added');
                return $this->redirect($this->generateUrl('admin_cms_edit', 
                    array(
                        'id' => $node->getId(),
                        'locale' => $locale
                    )
                ));
            }
        } 
        return $this->render('AdminBundle:Cms:addcms.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    public function editAction($id, $locale = 'en_EN')
    {
        $response = '';
        /**
         * @todo Get all tranlations and give them to the tamplate. Template missing ref to it
         **/
        $node = CmsQuery::create()
            ->joinWithI18n($locale, 'INNER JOIN')
            ->findPK($id);

        $form = $this->createForm(new CmsType(), $node);
        
        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);

            if ($form->isValid()) {
                
                $node->save();
                /**
                 * @todo give some feedback when done?
                 */
                $this->get('session')->setFlash('notice', 'cms.updated');
                //return $this->redirect($this->generateUrl('_account'));
            }
        }
        return $this->render('AdminBundle:Cms:editcmsi18n.html.twig', array(
            'form'      => $form->createView(),
            'node'      => $node,
            'notice'    => $response
        ));
        
    }

    public function updateCmsTreeAction()
    {
        $requests = $this->get('request');
        $nodes = $requests->get('data');

        $this->updateCmsTree($nodes);

        if ($this->getFormat() == 'json') {
            return $this->json_response(array(
                'status' => TRUE,
                'message' => $this->get('translator')->trans('save.changes.success', array(), 'admin'),
            ));
        }
    }

    /**
     * Updates all CMSnodes in the SQL
     * @param nestedSortable $nodes
     * @return type
     */
    protected function updateCmsTree($nodes)
    {
        // $sort: Array to keep track on the sort number associated with the parent id.
        // NestedSortable jQuery Plugin doesnt have a sort number, but the array are sorted.
        $sort = array();
        foreach ($nodes as $node) {
            if("root" == $node['item_id'])
                continue; // Root item from nestedSortable is not a page

            if (empty($sort[$node['parent_id']])) // Init the sort number to 1 if its not already is set
                $sort[$node['parent_id']] = 1;
            else // If sort number are set, increment it
                $sort[$node['parent_id']]++;

            $cmsNode = CmsQuery::create()->findOneById($node['item_id']);

            if("root" == $node['parent_id'])
                $cmsNode->setParentId(null);
            else
                $cmsNode->setParentId($node['parent_id']);
            $cmsNode->setSort($sort[$node['parent_id']]);
            $cmsNode->save();
        }
    }

    /*
     * Alternative method under construction
    protected function getFlatCmsTree()
    {
        // Get all nodes in Cms sorted by SORT and PARENTID
        $query = CmsQuery::create()
            ->filterByIsActive(TRUE)
            ->orderByParentId()
            ->orderBySort()
            ->joinCmsRelatedByParentId('sub')
        ;

        $result = $query->find();
        $menu = array();
        foreach ($result as $record) {
            //$menu[] = getChildren($record);
        }

        return $result;
    }*/

    /**
     * Creates the html for a System Tree of the CMS. Works recursivly.
     * @todo no-recursive: This could be done better, with an left join.
     * How? Too many Propel Calls.
     * @todo revove html from controller and make an array instead.
     * @param $int parent_id The parents ID
     * @return html ordered list
     */
    protected function getCmsTree($parent_id = NULL)
    {
        $t = $this->get('translator');
        $menu = '';
        $query = CmsQuery::create()
            ->filterByIsActive(TRUE)
            ->orderBySort()
        ;

        if (empty($parent_id)) {
            $query->filterByParentId(NULL, \Criteria::ISNULL);
        }
        else {
            $query->filterByParentId($parent_id);
        }

        $result = $query->find();

        if ($result->count()) {
            if (empty($parent_id))
                $menu .= '<ul id="sortable-list">';
            else
                $menu .= '<ul>';
            foreach($result as $record) {

                $path = $record->getPath();
                if ($record->getType() == 'frontpage') {
                    $path = ''; //Is not used.
                }

                $menu .= '<li id="item-' . $record->getId(). '" class="sortable-item ' . $record->getType() . '">';
                $menu .= '<div class="sort-handle record">';
                $menu .= '<span class="record-id">'.$record->getId().'</span>';
                $menu .= '<span class="record-title">' . $record->getTitle() . '</span>';
                $menu .= '<span class="record-type">' . $record->getType() . '</span>';
                $menu .= '<div class="actions">';
                $menu .= '<a href="'. $this->get('router')->generate('admin_cms_edit', array('id' => $record->getId())) .'" title="' . $t->trans('page.edit', array(), 'admin') . '" class="edit">' . $t->trans('page.edit', array(), 'admin') . '</a>';
                $menu .= '<a href="'. $this->get('router')->generate('admin_cms_delete', array('id' => $record->getId())) .'" title="' . $t->trans('page.delete', array(), 'admin') . '" class="delete">' . $t->trans('page.delete', array(), 'admin') . '</a>';
                $menu .= '</div>';
                $menu .= '</div>';


                // Retrieve all this nodes leafs/childrens
                $menu .= $this->getCmsTree($record->getId());

                //$menu .= '</li>';
            }

            $menu .= '</ul>';

        }

        return $menu;
    }

}
