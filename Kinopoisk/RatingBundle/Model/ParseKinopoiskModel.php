<?php

namespace Kinopoisk\RatingBundle\Model;

use Kinopoisk\RatingBundle\Model\HttpCurlModel;
use Symfony\Component\DomCrawler\Crawler;
use Kinopoisk\RatingBundle\Entity\Film;
use Kinopoisk\RatingBundle\Entity\FilmInfo;

/**
 * Модель для парсинга кинопоиска
 */
class ParseKinopoiskModel
{
    /**
     * Отпарсенные переменные
     * @var array
     */
    private $parseValues = array();
    /**
     * Экземпляр класса контроллер
     * @var
     */
    private $objController;

    /**
     * @param $url - url
     */
    public function __construct($url, $objController)
    {
        $this->objController = $objController;
        $client = new HttpCurlModel();
        $content = $client->setRequestMethod('get')->setUrl($url)->sendRequest()->getResponse();
        $crawler = new Crawler($content, $url);
        $tableTr = $this->parseTableTr($crawler);
        $this->parseValues = array(
            'positions' => $this->parsePositions($tableTr),
            'names' => $this->parseNames($tableTr),
            'ratings' => $this->parseRatings($tableTr),
            'votes' => $this->parseVotes($tableTr),
        );
    }

    /**
     * Находим tr в необходимой для парсинга таблице
     * @param $crawler - объект калсса Crawler
     * @return mixed
     */
    public function parseTableTr($crawler)
    {
        return $crawler->filter('td#block_left > div.block_left > table table')->eq(2)->filter('tr')->reduce(
            function ($node, $i) {
                if($i>12||$i<2){
                    return false;
                }

                return true;
            }
        );
    }

    /**
     * Парсим позиции в рейтинге
     * @param $tr
     * @return mixed
     */
    public function parsePositions($tr)
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
     * Парсим голоса
     * @param $tr - объект с данными тега tr таблицы с рейтингами
     * @return mixed
     */
    public function parseVotes($tr)
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
     * Парсим рейтинги
     * @param $tr - объект с данными тега tr таблицы с рейтингами
     * @return mixed
     */
    public function parseRatings($tr)
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
     * Парсим названия
     * @param $tr - объект с данными тега tr таблицы
     * @return mixed
     */
    public function parseNames($tr)
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
     * Геттер
     * @param $name - название поля
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
     *  Подготавливаем отпарсенные величины к вставке
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
                    $year = $this->parseYear($name);
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
     * Парсит год из названия фильма
     * @param $name - название фильма
     */
    public function parseYear(&$name)
    {
        $name = utf8_decode($name);
        if (preg_match('/([^(]+)\(([^)]+)\)/', $name, $matches)) {
            $name = trim($matches[1]);

            return (int)$matches[2];
        }

        return 0;
    }

    /**
     * Сохраняем полученные значения
     * @return array
     */
    public function saveParseValues()
    {
        $values = $this->prepareParseValues();

        foreach ($values as $value) {
            $this->saveFilmEntity($value);
        }

    }

    /**
     * Сохраняем сущность фильма
     * @param $params - параметры
     */
    public function saveFilmEntity($params)
    {
        $film = new Film();
        $em = $this->objController->getManager();
        $checkFilm = $em->getRepository('KinopoiskRatingBundle:Film')->findOneBy(
            array('name' => $params['name'], 'year' => $params['year'])
        );
        if (isset($checkFilm)) {
            $film = $checkFilm;
        } else {
            $film->setName($params['name']);
            $film->setYear($params['year']);
            $em->persist($film);
            $em->flush();
        }
        $this->saveFilmInfoEntiy($film, $params);
    }

    /**
     * Сохраняем информацию о фильме
     * @param $film - фильм
     * @param $params - параметры
     */
    public function saveFilmInfoEntiy(Film $film, $params)
    {
        $date = date("Y-m-d") . ' 0:0:1';
        $filmInfo = new FilmInfo();
        $em = $this->objController->getManager();
        $checkFilmInfo = $em->getRepository('KinopoiskRatingBundle:FilmInfo')->findOneBy(
            array('date' => new \DateTime($date), 'film' => $film)
        );
        if (isset($checkFilmInfo)) {
            $filmInfo = $checkFilmInfo;
        }

        $filmInfo->setDate(new \DateTime($date));
        $filmInfo->setFilm($film);
        $filmInfo->setRating($params['rating']);
        $filmInfo->setVote($params['vote']);
        $filmInfo->setPosition($params['position']);
        $em->persist($filmInfo);
        $em->flush();

    }


}
