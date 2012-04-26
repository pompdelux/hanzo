<?php /* vim: set sw=4: */
namespace Hanzo\Bundle\DataIOBundle;

use Symfony\Component\EventDispatcher\Event,
    Symfony\Component\Yaml\Parser,
    Symfony\Component\Yaml\Dumper,
    Symfony\Component\Yaml\Exception\ParseException
    ;

use Hanzo\Bundle\DataIOBundle\FilterUpdateEvent;

use Exception;

class UpdateListener
{
    public function onIncrementAssetsVersion(FilterUpdateEvent $event)
    {
      $file = __DIR__.'/../../../../app/config/config.yml';

      $yaml = new Parser();

      try 
      {
          $value = $yaml->parse(file_get_contents($file));
      } 
      catch (ParseException $e) 
      {
          throw new Exception( 'Updatelistener: Unable to parse the YAML string: %s', $e->getMessage() );
      }

      if ( isset( $value['framework']['templating'] ))
      {
          $value['framework']['templating']['assets_version'] = time();

          $dumper = new Dumper();

          $yaml = $dumper->dump($value,3);

          file_put_contents($file,$yaml);
      }
      else
      {
        throw new Exception( 'Updatelistener: config.yml is missing the framework -> templating block' );
      }
    }
}
