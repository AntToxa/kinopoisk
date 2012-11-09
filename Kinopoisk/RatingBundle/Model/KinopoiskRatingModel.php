<?php


namespace Kinopoisk\RatingBundle\Model;

use Kinopoisk\RatingBundle\Entity\Film;
use Kinopoisk\RatingBundle\Entity\FilmInfo;

/**
 * Модель для сохранении изменении информации о фильме
 */
class KinopoiskRatingModel
{
    /**
     * Экземпляр класса контроллер
     * @var
     */
    private $objController;

    public function __construct($objController)
    {
        $this->objController = $objController;
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
        $cache_id = 'filter_film_info_by_date_' . $date;
        $cacheDriver = $this->getCacheDriver();
        if ($cacheDriver->contains($cache_id)) {
            $cacheDriver->delete($cache_id);
        }

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

    /**
     * Пресдтавляем информацию о фильме в виде
     * ассоциотивного массива
     * @param $FilmInfo - информация о фильме
     * @return array
     */
    public function extractArrFilmInfo($FilmInfo){
        $return = array();
        foreach ($FilmInfo as $fi) {
            $return[] = array(
                'name' => $fi->getFilm()->getName(),
                'year' => $fi->getFilm()->getYear(),
                'rating' => $fi->getRating(),
                'vote' => $fi->getVote(),
                'position' => $fi->getPosition()
            );
        }
        return $return;
    }


    public function getFilterInfoByDate($date){


        $em = $this->objController->getManager();
        $cache_id = 'filter_film_info_by_date_' . $date;
        $cacheDriver = $this->getCacheDriver();
        if (!$cacheDriver->contains($cache_id)) {
            $FilmInfo = $em->getRepository('KinopoiskRatingBundle:FilmInfo')->findBy(
                array('date' => new \DateTime($date)),
                array('position' => 'ASC'),
                10
            );
            $FilmInfo = self::extractArrFilmInfo($FilmInfo);
            $cacheDriver->save($cache_id, $FilmInfo);
        }
        return $cacheDriver->fetch($cache_id);

    }

    public function getCacheDriver() {
        $memcache = new \Memcache();
        $memcache->connect('localhost', 11211);
        $cacheDriver = new \Doctrine\Common\Cache\MemcacheCache();
        $cacheDriver->setMemcache($memcache);
        return $cacheDriver;
    }


}
