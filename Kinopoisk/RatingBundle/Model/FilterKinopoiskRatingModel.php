<?php

namespace Kinopoisk\RatingBundle\Model;

/**
 * Модель для фильтрации данных
 */
class FilterKinopoiskRatingModel
{
    private $session;

    public function __construct($session)
    {
        $this->session = $session;
    }

    /**
     * Получаем значение даты в фильтре
     */
    public function getDate($format = false)
    {
        $date = $this->session->get('filter_date');
        if (!$date) {
            $date = date('d.m.Y');
            $this->setDate($date);
        }
        if($format){
            list($day,$month,$year) = explode('.',$date);
            $date = date($format,mktime(0, 0, 1, $month, $day, $year));
        }
        return $date;
    }

    /**
     * Устанавливаем дату в фильтре
     * @param $date - дата
     */
    public function setDate($date)
    {
        $this->session->set('filter_date', $date);
    }

}
