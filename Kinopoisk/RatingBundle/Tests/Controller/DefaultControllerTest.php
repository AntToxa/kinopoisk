<?php

namespace Kinopoisk\RatingBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    /**
     * тестируем _kinopoisk_rating
     */
    public function testIndex()
    {
        $client = static::createClient();
        $client->request('GET', '/kinopoisk_rating/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * Тестируем _kinopoisk_rating_filter
     */
    public function testFilter(){
        $client = static::createClient();
        $params = array(
            'filter_date' => '11.11.1111'
        );
        $client->request('POST', '/kinopoisk_rating/filter',$params);
        //$this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
        $this->assertEquals('{"responseCode":200,"film_info":[]}', $client->getResponse()->getContent());
        $params = array(
            'filter_date' => '11111111'
        );
        $client->request('POST', '/kinopoisk_rating/filter',$params);
        $this->assertEquals('{"responseCode":400,"film_info_error":"Incorect date!"}', $client->getResponse()->getContent());

    }
}
