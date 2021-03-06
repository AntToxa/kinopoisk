<?php
namespace Kinopoisk\RatingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Kinopoisk\RatingBundle\Model\FilterKinopoiskRatingModel;
use Symfony\Component\HttpFoundation\Response;
use Kinopoisk\RatingBundle\Model\KinopoiskRatingModel;


class DefaultController extends Controller
{


    /**
     * @Route("/", name="_kinopoisk_rating")
     * @Template()
     */
    public function indexAction()
    {
        $vars = array();
        $model = new KinopoiskRatingModel($this->getDoctrine());

        $filter = new FilterKinopoiskRatingModel($this->getRequest()->getSession());
        $date = $filter->getDate('Y-m-d H:i:s');
        $FilmInfo = $model->getFilterInfoByDate($date);

        $vars['FILM_INFO'] = $FilmInfo;

        return $vars;
    }

    /**
     * @Route("/filter", name="_kinopoisk_rating_filter")
     * Return a ajax response
     */
    public function filterAction()
    {
        $request = $this->get('request');
        $date = $request->request->get('filter_date');
        $model = new KinopoiskRatingModel($this->getDoctrine());

        if (preg_match('/[0-9]{2}\.[0-9]{2}\.[0-9]{4}/', $date)) {
            $filter = new FilterKinopoiskRatingModel($this->getRequest()->getSession());
            $filter->setDate($date);
            $date = $filter->getDate('Y-m-d H:i:s');
            $FilmInfo = $model->getFilterInfoByDate($date);

            $return = array("responseCode" => 200, "film_info" => $FilmInfo);
        } else {
            $return = array("responseCode" => 400, "film_info_error" => "Incorect date!");
        }

        $return = json_encode($return);

        return new Response($return, 200, array('Content-Type' => 'application/json'));
    }


}


