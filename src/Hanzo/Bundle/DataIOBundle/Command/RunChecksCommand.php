<?php /* vim: set sw=4: */

namespace Hanzo\Bundle\DataIOBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Translation\Exception\InvalidResourceException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Translation\Loader\XliffFileLoader;

class RunChecksCommand extends ContainerAwareCommand
{

    protected $errors = [];

    protected function configure()
    {
        $this->setName('hanzo:run-checks')
            ->setDescription('Run checks and tests before "allowing" deploy')
            ->addArgument('email', InputArgument::OPTIONAL, 'Email address of the one testing, if set only this person will get a status mail.')
        ;
    }


    /**
     * executes the job
     *
     * TODO: seperate validators into services
     *
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $status = $this->validateXliff();

        // must be last!
        if (false === getenv('TRAVIS')) {
            return $this->sendMail($input->getArgument('email'));
        }

        // see if these gets caught by travis and send in the report
        if (count($this->errors)) {
            echo implode("\n", $this->errors);
        }

        exit((int) !$status);
    }

    /**
     * validate xliff files for schematic errors
     */
    protected function validateXliff()
    {
        $dir = $this->getContainer()->get('kernel')->getRootDir() . '/Resources/translations';
        $finder = Finder::create()->files()->name('*.xliff')->in($dir);

        foreach ($finder as $file) {
            $path = $file->getRealPath();
            $name = $file->getFilename();
            list($domain, $locale, $junk) = explode('.', $name);

            try {
                $parser = new XliffFileLoader();
                $parser->load($path, $locale, $domain);
            } catch (InvalidResourceException $e) {
                $this->errors[] = $name . ":\n" .$e->getMessage()."\n";
            }
        }

        if (count($this->errors)) {
            return false;
        }

        return true;
    }


    /**
     * send "go" or "no go" mail
     *
     * @param  string $email optional override email
     */
    protected function sendMail($email)
    {
        $type = 'GO!';
        if (count($this->errors)) {
            $type = 'NEEEEEEEJ !!!! HVAD LAVER DU ???';
        }

        $recipients = [
            'hd@pompdelux.dk',
            'cc@pompdelux.dk',
            'un@bellcom.dk',
            'mmh@bellcom.dk',
            'ab@bellcom.dk',
        ];

        if ($email) {
            $recipients = [$email];
        }

        if ('GO!' == $type){
            $text = "Alle pre-deploy checks ok, der må deployes!";
        } else {
            $recipients[] = 'pdl@bellcom.dk';
            $text = "Såskudaogs! Der er fejl i skidtet, der må IKKE deployes!\n\nhttp://www.nooooooooooooooo.com/\n\n".implode("\n", $this->errors);
        }

        $text .= "\n\nmvh\n-- \nMr. Miyagi";

        $message = \Swift_Message::newInstance()
            ->setSubject('Hanzo deploy validation: '.$type)
            ->setFrom('pompdelux@pompdelux.dk')
            ->setReturnPath('pompdelux@pompdelux.dk')
            ->setTo($recipients)
            ->setBody($text)
        ;

        $this->getContainer()->get('mailer')->send($message);
    }
}
