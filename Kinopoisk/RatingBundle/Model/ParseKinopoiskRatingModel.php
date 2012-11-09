<?php

namespace Kinopoisk\RatingBundle\Model;

use Kinopoisk\RatingBundle\Model\HttpCurlModel;
use Symfony\Component\DomCrawler\Crawler;
use Kinopoisk\RatingBundle\Model\KinopoiskRatingModel;

/**
 * ������ ��� �������� ����������
 */
class ParseKinopoiskRatingModel
{
    /**
     * ����������� ����������
     * @var array
     */
    private $parseValues = array();

    /**
     * ������ ��� ������ � FilmInfo
     * @var KinopoiskRatingModel
     */
    private $modelRatingKinopoisk;

    /**
     * @param $url - url
     */
    public function __construct($url, $objController)
    {
        $content = $this->getContent($url);
        $tableTr = self::getCrowlerTr($content);
        $this->setParseValues($tableTr);
        $this->modelRatingKinopoisk = new KinopoiskRatingModel($objController);

    }

    /**
     * �������� ��������� ������ Crawler
     * @param $content - html �������
     * @param $url - ���
     * @return mixed
     */
    public static function getCrowlerTr($content){
        $crawler = new Crawler($content);
        return self::parseTableTr($crawler);
    }


    /**
     * ������������� ����������� ��������
     * @param $tableTr - ������ � ������� ����� tr ������� � ����������
     */
    protected function setParseValues($tableTr){
        $this->parseValues = array(
            'positions' => self::parsePositions($tableTr),
            'names' => self::parseNames($tableTr),
            'ratings' => self::parseRatings($tableTr),
            'votes' => self::parseVotes($tableTr),
        );
    }

    /**
     * �������� ������� �� ����
     * @param $url - url
     */
    protected function getContent($url) {
        $client = new HttpCurlModel();
        $content = $client->setRequestMethod('get')->setUrl($url)->sendRequest()->getResponse();
        return $content;
    }

    /**
     * ������� tr � ����������� ��� �������� �������
     * @param $crawler - ������ ������ Crawler
     * @return mixed
     */
    public static function parseTableTr($crawler)
    {
        return $crawler->filter('td#block_left > div.block_left > table table')->eq(2)->filter('tr')->reduce(
            function ($node, $i) {
                if($i>11||$i<2){
                    return false;
                }

                return true;
            }
        );
    }

    /**
     * ������ ������� � ��������
     * @param $tr- ������ � ������� ���� tr ������� � ����������
     * @return mixed
     */
    public static function parsePositions($tr)
    {
        return $tr->filter('td')->reduce(
            function ($node, $i) {
                return $i % 4 == 0;
            }
        )->each(
            function ($node, $i) {
                return intval($node->nodeValue);
            }
        );
    }

    /**
     * ������ ������
     * @param $tr - ������ � ������� ���� tr ������� � ����������
     * @return mixed
     */
    public static function parseVotes($tr)
    {
        return $tr->filter('td')->reduce(
            function ($node, $i) {
                return $i % 4 == 2;
            }
        )->filter('span')->each(
            function ($node, $i) {
                return intval(preg_replace('/[^0-9]/', '', $node->nodeValue));
            }
        );
    }

    /**
     * ������ ��������
     * @param $tr - ������ � ������� ���� tr ������� � ����������
     * @return mixed
     */
    public static function parseRatings($tr)
    {
        return $tr->filter('td')->reduce(
            function ($node, $i) {
                return $i % 4 == 2;
            }
        )->filter('a.continue')->each(
            function ($node, $i) {
                return $node->nodeValue;
            }
        );
    }

    /**
     * ������ ��������
     * @param $tr - ������ � ������� ���� tr �������
     * @return mixed
     */
    public static function parseNames($tr)
    {
        return $tr->filter('td')->reduce(
            function ($node, $i) {
                return $i % 4 == 1;
            }
        )->filter('a.all')->each(
            function ($node, $i) {
                return $node->nodeValue;
            }
        );
    }

    /**
     * ������ ��� �� �������� ������
     * @param $name - �������� ������
     */
    public static function parseYear(&$name)
    {
        //$name = utf8_decode($name);
        if (preg_match('/([^(]+)\(([^)]+)\)/', $name, $matches)) {
            $name = trim($matches[1]);

            return (int)$matches[2];
        }

        return 0;
    }

    /**
     * ������
     * @param $name - �������� ����
     * @return array
     */
    public function __get($name)
    {
        if (isset($this->parseValues[$name]) && is_array($this->parseValues[$name])) {
            return $this->parseValues[$name];
        }

        return array();
    }

    /**
     *  �������������� ����������� �������� � �������
     * @return array
     */
    private function prepareParseValues()
    {
        $return = array();
        $names = $this->names;
        $ratings = $this->ratings;
        $votes = $this->votes;
        foreach ($this->positions as $ind => $position) {
            if ($position) {
                if (isset($names[$ind])) {
                    $name = $names[$ind];
                    $year = self::parseYear($name);
                }
                if (isset($ratings[$ind])) {
                    $rating = $ratings[$ind];
                }
                if (isset($votes[$ind])) {
                    $vote = (int)$votes[$ind];
                }
                if ($year && $rating && $vote && $name) {
                    $return[] = array(
                        'year' => $year,
                        'rating' => $rating,
                        'name' => $name,
                        'vote' => $vote,
                        'position' => $position
                    );

                }
            }
        }

        return $return;
    }


    /**
     * ��������� ���������� ��������
     * @return array
     */
    public function saveParseValues()
    {
        $values = $this->prepareParseValues();

        foreach ($values as $value) {
            $this->modelRatingKinopoisk->saveFilmEntity($value);
        }

    }




}
