<?php

namespace Hanzo\Bundle\CMSBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    protected function getClient()
    {
        $client = static::createClient([
            'environment' => 'test_dk',
            'debug'       => false,
            ],[
            'HTTP_HOST' => 'www.pompdelux.com'
        ]);
        $client->insulate();

        return $client;
    }


    public function testIndex()
    {
        $crawler = $this->getClient()->request('GET', '/');
        $this->assertEquals('Select your country', $crawler->filter('h1')->text());
    }

    public function testCountryIndex()
    {
        $crawler = $this->getClient()->request('GET', '/da_DK/');

        $this->assertGreaterThan(0, $crawler->filter('html:contains("POMPdeLUX ApS")')->count());
        $this->assertEquals('http://www.pompdelux.com/da_DK/account', $crawler->selectLink('Min side')->link()->getUri());
    }
}
