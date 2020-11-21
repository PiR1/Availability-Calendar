<?php
/**
 * Copyright (C) PiR1, Inc - All Rights Reserved
 *    Apache License
 *    Version 2.0, January 2004
 *    http://www.apache.org/licenses/
 *    See Licence file
 *
 * @file      Ical.php
 * @author    PiR1
 * @date     25/05/2020 23:25
 */

namespace Calendar\model;

class Ical
{

    /**
     * @var integer
     */
    private $id;
    /**
     * @var string
     */
    private $url;
    /**
     * @var string
     */
    private $type;

    /**
     * ical constructor.
     */
    public function __construct()
    {
    }

    /**
     * ical creator.
     * @param $url
     * @param $type
     * @return Ical
     */
    public static function create($url, $type)
    {
        $self=new self();
        $self->url=$url;
        $self->type=$type;
        return $self;
    }


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }


    public function __toString()
    {
        return "{\"id\":".$this->id.","."\"url\":\"".$this->url."\",\"type\":\"".$this->type."\"}";
    }
    public function toArray(){
        return["id"=>$this->id, "url"=>$this->url, "type"=>$this->type];
    }



}