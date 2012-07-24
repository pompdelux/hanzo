<?php

namespace Hanzo\Bundle\ConsultantNewsletterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Hanzo\Core\CoreController;
use Hanzo\Core\Hanzo;
use Hanzo\Core\Tools;

use Symfony\Component\Finder\Finder;

class DefaultController extends CoreController
{
    
    public function indexAction()
    {
        return $this->render('ConsultantNewsletterBundle:Default:index.html.twig',
        	array(
        		'page_type' => 'consultant'
        	)
        );
    }

    public function fileManagerAction()
    {
    	$finder = new Finder();
    	$finder->files()->in(__DIR__.'/../../../../../web/fx/images');
    	$finder->files()->name('*.jpg');
    	$finder->files()->name('*.png');
    	$finder->files()->name('*.gif');
    	$finder->sortByName();
    	$images = array();

		foreach ($finder as $file) {
		    $images[] = array(
		    	'absolute' 	=> $this->container->get('router')->getContext()->getBaseUrl().'/fx/images/'.$file->getRelativePathname(),
		    	'relative' 	=> '/fx/images/'.$file->getRelativePathname(),
		    	'name'		=> $file->getFilename()
		    );
		}
        return $this->render('ConsultantNewsletterBundle:Default:filemanager.html.twig',
        	array(
        		'images' => $images
        	)
        );
    }
}
