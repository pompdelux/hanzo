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
use Hanzo\Model\AddressesQuery;

use Hanzo\Model\Orders;
use Hanzo\Model\OrdersQuery;

use Hanzo\Model\Cms;
use Hanzo\Model\CmsI18n;
use Hanzo\Model\CmsI18nQuery;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends RawMinkContext implements Context, SnippetAcceptingContext
{
    use Behat\Symfony2Extension\Context\KernelDictionary;

    /** @var \Behat\MinkExtension\Context\MinkContext */
    private $minkContext;

    private $data = [];

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
            $screenshotFileName = date('d-m-y').'-'.uniqid().'.png';
            $pageFileName       = date('d-m-y').'-'.uniqid().'.html';
            $filePath           = $this->getContainer()->get('kernel')->getRootdir().'/../../';

            file_put_contents($filePath.$screenshotFileName, $screenshot);
            file_put_contents($filePath.$pageFileName, $page);
            print 'Screenshot at: '.$filePath.$screenshotFileName."\n";
            print 'HTML dump at: '.$filePath.$pageFileName."\n";
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

    /**
     * @When I wait until Ajax is done
     */
    public function iWaitUntilAjaxIsDone()
    {
        $this->waitForJquery(5000);
    }

    /**
     * @When /^I hover over the element "([^"]*)"$/
     */
    public function iHoverOverTheElement($locator)
    {
        $session = $this->getSession(); // get the mink session
        $element = $session->getPage()->find('css', $locator); // runs the actual query and returns the element

        // errors must not pass silently
        if (null === $element) {
            throw new \InvalidArgumentException(sprintf('Could not evaluate CSS selector: "%s"', $locator));
        }

        // ok, let's hover it
        $element->mouseOver();
    }

    /**
     * @Transform table:title,active,on_mobile,only_mobile
     * @param TableNode $menuItemsTable
     */
    public function castMenuItemsTable(TableNode $menuItemsTable)
    {
        $menuItems = [];
        foreach ($menuItemsTable->getHash() as $menuItemHash)
        {
            $cmsNode = CmsI18nQuery::create()->findOneByTitle($menuItemHash['title']);

            if (!$cmsNode instanceOf CmsI18n)
            {
                $cmsNode = new Cms();
                $cmsNode->setType('page');
                $cmsNode->setCmsThreadId(23);
                $cmsNode->setParentId(NULL);

                $cmsI18N = new CmsI18n();
                $cmsI18N->setCms($cmsNode);
                $cmsI18N->setTitle($menuItemHash['title']);
                $cmsI18N->setLocale('da_DK');
                $cmsI18N->setContent($menuItemHash['title']);
                $cmsI18N->setOnMobile($menuItemHash['on_mobile']);
                $cmsI18N->setOnlyMobile($menuItemHash['only_mobile']);

                $cmsNode->save();
                $cmsI18N->save();
            }

            $menuItems[] = $cmsNode;

        }
        return $menuItems;
    }

    /**
     * @Given the following menu items exist:
     */
    public function theFollowingMenuItemsExist(array $menuItems)
    {
        // Not sure what to do here :)
    }

    /**
     * @Given I am on a category page
     */
    public function iAmOnACategoryPage()
    {
        $categoryUrl = $this->getMinkParameter('base_url');
        $this->getSession()->visit($categoryUrl.'pige/undertoej');
    }

    /**
     * @When /^I click the element "([^"]*)"$/
     */
    public function iClickTheElement($locator)
    {
        $session = $this->getSession(); // get the mink session
        $element = $session->getPage()->find('css', $locator); // runs the actual query and returns the element

        // errors must not pass silently
        if (null === $element) {
            throw new \InvalidArgumentException(sprintf('Could not evaluate CSS selector: "%s"', $locator));
        }

        $element->click();
    }

    /**
     * @param mixed $duration
     */
    protected function waitForJquery($duration)
    {
        $this->getSession()->wait($duration, "(0 === jQuery.active && 0 === jQuery(':animated').length)");
    }

    /**
     * @When I wait until the entire menu is visible
     */
    public function iWaitUntilTheEntireMenuIsVisible()
    {
        $this->waitForJquery(5000);
    }

    /**
     * @Given I am logged in as customer
     */
    public function iAmLoggedInAsCustomer()
    {
        $session = $this->getSession();
        $driver = $session->getDriver();

        if (!$driver instanceof Behat\Mink\Driver\Selenium2Driver) {
            return;
        }

        $customer = CustomersQuery::create()
            ->filterByEmail('hf+test1@bellcom.dk')
            ->findOne();

        if (!( $customer instanceof Customers )) {
            throw new Exception('Customer not found');
        }

        $this->data['current_customer'] = $customer;

        // Needed in order to set domain, else it will fail with "Can only set Cookies for the current domain"
        $session->visit($this->getMinkParameter('base_url'));

        $token = new UsernamePasswordToken($customer, null, 'secured_area', $customer->getRoles());
        $this->getContainer()->get('security.context')->setToken($token);
        $this->getContainer()->get('session')->set('_security_secured_area', serialize($token));
        $this->getContainer()->get('session')->save();

        $sessionName = $this->getContainer()->get('session')->getName();
        $sessionId   = $this->getContainer()->get('session')->getId();

        $driver->setCookie($sessionName, $sessionId);

        $this->data['web_session'] = [];
        $this->data['web_session']['session_name'] = $sessionName;
        $this->data['web_session']['session_id'] = $sessionId;

        $session->visit($this->getMinkParameter('base_url').'account');
        $this->assertSession()->elementTextContains('css', '.legends .edit + span', 'Rediger din ordre');
    }

    /**
     * @Given I edit the order
     */
    public function iEditTheOrder()
    {
        if (!isset($this->data['order_from_consultant_site'])) {
            throw new Exception('Order not found');
        }
        $session = $this->getSession();
        $session->visit($this->getMinkParameter('base_url').'account');

        $this->assertSession()->elementTextContains('css', '#order-status td', '#'.$this->data['order_from_consultant_site']);

        $this->getSession()->getPage()->clickLink('Redigér ordre');

        $this->waitForJquery(5000);

        $this->assertSession()->elementTextContains('css', '.dialoug.confirm > h2', 'Bemærk!');

        $this->iClickTheElement('.dialoug-confirm');
    }

    /**
     * @Then I should only see products which are in the active product range
     */
    public function iShouldOnlySeeProductsWhichAreInTheActiveProductRange()
    {
        // throw new PendingException();
    }

    /**
     * @Given there are no orders
     */
    public function thereAreNoOrders()
    {
        if (!isset($this->data['current_customer'])) {
            throw new Exception('Customer not found');
        }
        $customer = $this->data['current_customer'];

        $orders = OrdersQuery::create()
            ->filterByCustomersId($customer->getId())
            ->find();

        $service = $this->getContainer()->get('hanzo.core.orders_service');

        $orderIds = [];
        foreach ($orders as $order) {
            $orderIds[] = $order->getId();
        }

        if (empty($orderIds)) {
            return;
        }

        // To much voodoo to delete an order
        $connection = \Propel::getConnection();
        $sql = "DELETE FROM orders WHERE id IN (".implode(',', $orderIds).")";
        $stmt = $connection->prepare($sql);
        $stmt->execute();
    }

    /**
     * @Given a consultant creates an order with the following products:
     */
    public function aConsultantCreatesAnOrderWithTheFollowingProducts(TableNode $table)
    {
        if (!isset($this->data['current_customer'])) {
            throw new Exception('Customer not found');
        }

        // Ensure that values exists:
        $customer = $this->data['current_customer'];
        $sessionId = $this->data['web_session']['session_id'];

        // TODO not hardcode id:
        $eventId = 35965;

        $order = new Orders();
        $order->setCustomersId($customer->getId());
        $order->setEmail($customer->getEmail());
        $order->setFirstName($customer->getFirstName());
        $order->setLastName($customer->getLastName());
        $order->setState(Orders::STATE_PENDING);
        $order->setSessionId($sessionId);

        $order->setAttribute('SalesResponsible', 'global', 'WEB DK');
        $order->setEventsId($eventId);
        $order->setAttribute('HomePartyId', 'global', 'WEB DK');

        $order->setAttribute('domain_key', 'global', 'SalesDK');
        $order->setCurrencyCode('DKK');

        $order->setDeliveryMethod(11);

        // Make sure the test customer has a company_shipping address
        $address = AddressesQuery::create()
            ->filterByCustomersId($customer->getId())
            ->filterByType('company_shipping') // 11
            ->findOne();
        $order->setDeliveryAddress($address);

        $address = AddressesQuery::create()
            ->filterByCustomersId($customer->getId())
            ->filterByType('payment')
            ->findOne();

        $order->setBillingAddress($address);

        $order->save();

        $orderId = $order->getId();

        $this->data['order_from_consultant_site'] = $orderId;
    }
}
