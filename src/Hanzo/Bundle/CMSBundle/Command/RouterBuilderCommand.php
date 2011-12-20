<?php
namespace Hanzo\Bundle\CMSBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Hanzo\Model\Cms;
use Hanzo\Model\CmsPeer;
use Hanzo\Model\CmsQuery;

/**
 * @see: http://symfony.com/doc/2.0/cookbook/console.html
 */
class RouterBuilderCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
          ->setName('hanzo:router:builder')
          ->setDescription('Generate the router files')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
      $result = CmsQuery::create()
        ->joinCmsI18n(NULL, 'INNER JOIN')
        ->orderByParentId()
        ->useCmsI18nQuery()
          ->orderByLocale()
        ->endUse()
        ->find()
      ;

      $buffer = array();
      $counter = 1;
      $processed = array();
      foreach ($result as $record) {
        foreach ($record->getCmsI18ns() as $item){

          $id = $item->getId();
          $path = trim($item->getPath());
          $locale = trim($item->getLocale());
          $type = trim($record->getType());
          $title = trim($item->getTitle());

          if ('' == $title) {
            continue;
          }

          if (isset($processed[$path.'.'.$locale])) {
            continue;
          }
          $processed[$path.'.'.$locale] = $path;

          if (!isset($buffer[$locale])) {
            $buffer[$locale] = '';
          }

          $settings = $item->getSettings();
          if (substr($settings, 0, 2) == 'a:') {
            $settings = unserialize(stripslashes($settings));
          }

          switch ($type) {
            case 'category':
              $buffer[$locale] .= trim("
category_" . $id . "_" . strtolower($locale) . ":
    pattern: /{$path}/{pager}
    defaults:
        _controller: HanzoCategoryBundle:Default:view
        _format: html
        cms_id: {$id}
        category_id: {$settings['category_id']}
        pager: 1
    requirements:
        pager: \d+
        _format: html|json
product_" . $id . "_" . strtolower($locale) . ":
    pattern: /{$path}/{product_id}/{title}
    defaults:
        _controller: HanzoProductBundle:Default:view
        _format: html
        product_id: 0
        cms_id: {$id}
        category_id: {$settings['category_id']}
        title: ''
    requirements:
        id: \d+
        _format: html|json
")."\n";
              break;
            case 'page':
              $buffer[$locale] .= trim("
page_" . $id . "_" . strtolower($locale) . ":
    pattern: /{$path}
    defaults:
        _controller: HanzoCMSBundle:Default:view
        id: {$id}
")."\n";
              break;
            case 'system':
              switch ($settings['view']) {
                case 'mannequin':
                  $buffer[$locale] .= trim("
system_" . $id . "_" . strtolower($locale) . ":
    pattern: /{$path}
    defaults:
        _controller: HanzoMannequinBundle:Default:view
        id: {$id}
")."\n";
                  break;
                case 'newsletter':
                  $buffer[$locale] .= trim("
newsletter_" . $id . "_" . strtolower($locale) . ":
    pattern: /{$path}
    defaults:
        _controller: HanzoNewsletterBundle:Default:view
        id: {$id}
")."\n";
                  break;
                case 'category_search':
                case 'advanced_search':
                  $method = explode('_', $settings['view']);
                  $method = array_shift($method);
                  $buffer[$locale] .= trim("
search_" . $id . "_" . strtolower($locale) . ":
    pattern: /{$path}
    defaults:
        _controller: HanzoSearchBundle:Default:{$method}
        _format: html
        id: {$id}
    requirements:
        _format: html|json
")."\n";
                  break;
                default:
                  print_r($settings);
              }
              break;
            case 'url': // ignore
              continue;
              break;
          }

          $counter++;
        }
      }

      $file = __DIR__ . '/../Resources/config/cms_routing.yml';
      $out = '';
      $time = time();
      foreach ($buffer as $locale => $routers) {
        $out .= "# -:[{$locale} : ".date('Y-m-d H:i:s', $time)."]:-

" . trim($routers) . "

# --------------------------------------------
";
      }
      file_put_contents($file, $out);

      $output->writeln('Routers saved to: <info>'.$file.'</info>');

      // clear cache after updating routers
      $command = $this->getApplication()->find('cache:clear');
      $arguments = array(
          'command' => 'cache:clear',
          '--env' => 'prod'
      );

      $input = new ArrayInput($arguments);
      $returnCode = $command->run($input, $output);

    }
}
