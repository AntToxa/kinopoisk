<?php

namespace Kinopoisk\RatingBundle\Model;

/**
 * ������ ��� ���������� ������
 */
class FilterKinopoiskRatingModel
{
    private $session;

    public function __construct($session)
    {
        $this->session = $session;
    }

    /**
     * �������� �������� ���� � �������
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
     * ������������� ���� � �������
     * @param $date - ����
     */
    public function setDate($date)
    {
        $this->session->set('filter_date', $date);
    }

}
