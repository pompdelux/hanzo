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

      if ( !is_file($file) )
      {
        throw new Exception( 'UpdateListener: could not find config.yml at: "'.$file.'"' );
      }

      if ( !is_writeable($file) )
      {
        throw new Exception( 'UpdateListener: config.yml at: "'.$file.'" is not writeable' );
      }

      $yaml = new Parser();

      try 
      {
          $value = $yaml->parse(file_get_contents($file));
      } 
      catch (ParseException $e) 
      {
          throw new Exception( 'UpdateListener: Unable to parse the YAML string: %s', $e->getMessage() );
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
        throw new Exception( 'UpdateListener: config.yml is missing the framework -> templating block' );
      }
    }

    /**
     * onUpdateTranslations
     * @return void
     * @author Henrik Farre <hf@bellcom.dk>
     **/
    public function onUpdateTranslations(FilterUpdateEvent $event)
    {
        chdir( __DIR__.'/../../../../app/Resources/translations/' );
        $command = '/usr/bin/git pull';
        exec($command, $out, $return);
        error_log(__LINE__.':'.__FILE__.' '.$return .' '.print_r($out,1)); // hf@bellcom.dk debugging
    }
}
