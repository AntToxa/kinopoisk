<?php
namespace Kinopoisk\RatingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Kinopoisk\RatingBundle\Model\FilterKinopoiskRatingModel;
use Kinopoisk\RatingBundle\Entity\FilmInfo;
use Symfony\Component\HttpFoundation\Response;


class DefaultController extends Controller
{


    /**
     * @Route("/", name="_kinopoisk_rating")
     * @Template()
     */
    public function indexAction()
    {
        $vars = array();
        $filter = new FilterKinopoiskRatingModel($this->getRequest()->getSession());
        $date = $filter->getDate('Y-m-d H:i:s');
        $em = $this->getDoctrine()->getManager();
        $FilmInfo = $em->getRepository('KinopoiskRatingBundle:FilmInfo')->findBy(
            array('date' => new \DateTime($date)),array('position'=>'ASC'),10
        );

        $vars['FILM_INFO'] = $FilmInfo;
        return $vars;
    }

    /**
     * @Route("/filter", name="_kinopoisk_rating_filter")
     * Return a ajax response
     */
    public function filterAction(){
        $request = $this->get('request');
        $date=$request->request->get('filter_date');
        if(preg_match('/[0-9]{2}\.[0-9]{2}\.[0-9]{4}/',$date)){
            $filter = new FilterKinopoiskRatingModel($this->getRequest()->getSession());
            $filter->setDate($date);
            $date = $filter->getDate('Y-m-d H:i:s');
            $em = $this->getDoctrine()->getManager();
            $FilmInfo = $em->getRepository('KinopoiskRatingBundle:FilmInfo')->findBy(
                array('date' => new \DateTime($date)),array('position'=>'ASC'),10
            );
            $FilmInfoArray = array();
            foreach($FilmInfo as $fi){
                $FilmInfoArray[] = array(
                    'name' => $fi->getFilm()->getName(),
                    'year' => $fi->getFilm()->getYear(),
                    'rating' => $fi->getRating(),
                    'vote' => $fi->getVote(),
                    'position' => $fi->getPosition()
                );
            }
            $return=array("responseCode"=>200,  "film_info"=>$FilmInfoArray);
        }
        else{
            $return=array("responseCode"=>400, "film_info_error"=>"Incorect date!");
        }

        $return=json_encode($return);
        return new Response($return,200,array('Content-Type'=>'application/json'));//make sure it has the correct content type
    }


}


