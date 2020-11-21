<?php
/**
 * Copyright (C) PiR1, Inc - All Rights Reserved
 *    Apache License
 *    Version 2.0, January 2004
 *    http://www.apache.org/licenses/
 *    See Licence file
 *
 * @file      Event.php
 * @author    PiR1
 * @date     25/05/2020 23:25
 */

namespace Calendar\model;
class Event
{

    // table columns
    private $id;
    private $start;
    private $end;
    private $description;
    private $uid;
    private $idCal;


    /**
     * Event constructor.
     */
    public function __construct()
    {

    }

    /**
     * Event creator.
     * @param $start
     * @param $end
     * @param $description
     * @return Event
     */
    public static function create($start, $end, $description)
    {
        $self = new self();
        $self->start = $start;
        $self->end = $end;
        $self->description = $description;
        return $self;
    }



    /**
     * Transform user attributes to array
     * @return array
     */
    public function toArray(){
        return array(
            "start" => $this->start,
            "end" => $this->end,
            "description" => $this->description
        );
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @param mixed $start
     */
    public function setStart($start)
    {
        $this->start = $start;
    }

    /**
     * @return mixed
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * @param mixed $end
     */
    public function setEnd($end)
    {
        $this->end = $end;
    }

    /**
     * @return mixed
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * @param mixed $uid
     */
    public function setUid($uid): void
    {
        $this->uid = $uid;
    }


    /**
     * @return mixed
     */
    public function getIdCal()
    {
        return $this->idCal;
    }

    /**
     * @param mixed $idCal
     */
    public function setIdCal($idCal)
    {
        $this->idCal = $idCal;
    }
    

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }



}
