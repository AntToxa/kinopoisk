<?php

namespace Kinopoisk\RatingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Kinopoisk\RatingBundle\Entity\FilmInfo
 *
 * @ORM\Table(name="film_info")
 * @ORM\Entity
 */
class FilmInfo
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer $position
     *
     * @ORM\Column(name="position", type="integer", nullable=false)
     */
    private $position;

    /**
     * @var float $rating
     *
     * @ORM\Column(name="rating", type="decimal", nullable=false)
     */
    private $rating;

    /**
     * @var integer $vote
     *
     * @ORM\Column(name="vote", type="integer", nullable=false)
     */
    private $vote;

    /**
     * @var \DateTime $date
     *
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    private $date;

    /**
     * @var Film
     *
     * @ORM\ManyToOne(targetEntity="Film")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="film_id", referencedColumnName="id")
     * })
     */
    private $film;



    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return FilmInfo
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set rating
     *
     * @param float $rating
     * @return FilmInfo
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating
     *
     * @return float
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set vote
     *
     * @param integer $vote
     * @return FilmInfo
     */
    public function setVote($vote)
    {
        $this->vote = $vote;

        return $this;
    }

    /**
     * Get vote
     *
     * @return integer
     */
    public function getVote()
    {
        return $this->vote;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return FilmInfo
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set film
     *
     * @param Kinopoisk\RatingBundle\Entity\Film $film
     * @return FilmInfo
     */
    public function setFilm(\Kinopoisk\RatingBundle\Entity\Film $film = null)
    {
        $this->film = $film;

        return $this;
    }

    /**
     * Get film
     *
     * @return Kinopoisk\RatingBundle\Entity\Film
     */
    public function getFilm()
    {
        return $this->film;
    }
}