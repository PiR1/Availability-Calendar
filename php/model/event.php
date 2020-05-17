<?php
class Event
{

    // table columns
    private $id;
    private $start;
    private $end;
    private $description;

    /**
     * Event constructor.
     * @param $connection
     * @param string $table_name
     * @param $id
     * @param $start
     * @param $end
     * @param $description
     */
    public function __construct($start, $end, $description)
    {
        $this->table_name = "events";
        $this->start = $start;
        $this->end = $end;
        $this->description = $description;
    }


    /**
     * Transform user attributes to array 
     *
     * @return array
     */
    public function toArray(){
        $event = array(
            "start" => $this->start,
            "end" => $this->end,
            "description" => $this->description
        );
        return $event;
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
