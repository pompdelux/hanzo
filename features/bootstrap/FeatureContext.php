<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Testwork\Tester\Result\TestResult;
use Behat\MinkExtension\Context\RawMinkContext;

use Hanzo\Model\Customers;
use Hanzo\Model\CustomersQuery;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends RawMinkContext implements Context, SnippetAcceptingContext
{
    use Behat\Symfony2Extension\Context\KernelDictionary;

    /** @var \Behat\MinkExtension\Context\MinkContext */
    private $minkContext;

    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();

        $this->minkContext = $environment->getContext('Behat\MinkExtension\Context\MinkContext');
    }

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
    }

    /**
     * @AfterStep
     */
    public function takeScreenshotAfterFailedStep(AfterStepScope $scope)
    {
        if (TestResult::FAILED === $scope->getTestResult()->getResultCode()) {
            // http://behat.readthedocs.org/en/v3.0/cookbooks/context_communication.html
            $driver      = $this->minkContext->getSession()->getDriver();

            if (!$driver instanceof Behat\Mink\Driver\Selenium2Driver) {
                return;
            }

            $page               = $this->minkContext->getSession()->getPage()->getContent();
            $screenshot         = $driver->getScreenshot();
            $screenshotFileName = date('d-m-y') . '-' . uniqid() . '.png';
            $pageFileName       = date('d-m-y') . '-' . uniqid() . '.html';
            $filePath           = $this->getContainer()->get('kernel')->getRootdir() . '/../../';

            file_put_contents($filePath.$screenshotFileName, $screenshot);
            file_put_contents($filePath.$pageFileName, $page);
            print 'Screenshot at: ' . $filePath.$screenshotFileName."\n";
            print 'HTML dump at: ' . $filePath.$pageFileName."\n";
        }
    }

    /**
     * @Given I go to a product page
     */
    public function iGoToAProductPage()
    {
        $this->getSession()->visit($this->getMinkParameter('base_url').'pige/jakker/14376/geneva-lt-sommerjakke?focus=23710');
        // $this->assertSession()->elementTextContains('css', '.productdescription .tabs .current', 'Varebeskrivelse');
    }

    /**
     * @Given there are the following users:
     *
     * TODO: Consulent VS Customers
     * Or use transform tag:
     * http://docs.behat.org/en/latest/guides/2.definitions.html#step-argument-transformations
     */
    public function thereAreTheFollowingUsers(TableNode $usersTable)
    {
        $users = [];
        foreach ($usersTable->getHash() as $userHash) {
            $customer = CustomersQuery::create()
                ->filterByEmail($userHash['email'])
                ->filterByPassword(sha1($userHash['password']))
                ->findOne();

            if (!$customer instanceof Customers) {
                $customer = new Customers();
                $customer->setEmail($userHash['email']);
                $customer->setPassword(sha1($userHash['password']));
                $customer->setPasswordClear($userHash['password']);
                $customer->save();
            }
        }

        $users[] = $customer;
    }

    /**
     * @Given I am on frontpage
     */
    public function iAmOnFrontpage()
    {
        $urlParts = parse_url($this->getMinkParameter('base_url'));
        $this->getSession()->visit( $urlParts['scheme'].'://'.$urlParts['host'] );
    }
}
