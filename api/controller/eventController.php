<?php
/**
 * Copyright (C) PiR1, Inc - All Rights Reserved
 *    Apache License
 *    Version 2.0, January 2004
 *    http://www.apache.org/licenses/
 *    See Licence file
 *
 * @file      eventController.php
 * @author    PiR1
 * @date     25/05/2020 23:25
 */

namespace Calendar\controller;

use Calendar\model\Event;

use DateTime;
use Exception;
use \PDO;
use stdClass;

class eventController extends controller
{

    /**
     * eventController constructor.
     * @param $connection
     * @throws Exception
     */
    public function __construct($connection)
    {
        parent::__construct($connection, "events", "\Calendar\model\Event");
    }


    /**
     * Read all events.
     *
     * @throws Exception
     */
    public function getAll()
    {
        $events=$this->fetchAll();
        if (sizeof($events)>0){

        $event_arr=array();
        // retrieve our table contents
       foreach ($events as $event){
            // extract row, transform $row['key']=value to $key=value

            $ev = array(
                "start" => $event->getStart(),
                "end" => $event->getEnd()
            );

            // array_push($event_arr["records"], $event);
            array_push($event_arr, $ev);
        }

        // set response code - 200 OK
        http_response_code(200);

        // show meridiens data in json format
        echo json_encode($event_arr);
        }

}

    /**
     * Check if an event exists
     *
     * @param Event $event
     * @return boolean
     */

    public function check($event)
    {

        // query to read single record
        $query = "SELECT * FROM " . $this->tableName . "
                WHERE
                    start=:start, end=:end
                LIMIT
                    0,1";
        // prepare query statement
        $stmt = $this->connection->prepare($query);

        // bind values
        $stmt->bindValue(":start", htmlspecialchars(strip_tags($event->getStart())));
        $stmt->bindValue(":end", htmlspecialchars(strip_tags($event->getEnd())));

        // execute query
        $stmt->execute();

        // get retrieved row
        $stmt->fetch(PDO::FETCH_ASSOC);

        // check if there are at least 1 match
        if ($stmt->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Update the state of a date
     * @param $data
     * @throws Exception
     */
    public function updateDateState($data)
    {
        $date = new DateTime($data->date);

        $query = "select * from " . $this->tableName . " Where :date >=start and :date <=end ";

        $stmt = $this->connection->prepare($query);
        $stmt->bindValue(":date", $this->prepareValue($date->format("Y-m-d")));

        $stmt->execute();

        $row = $stmt->fetchAll(PDO::FETCH_CLASS,$this->model);
        $yesterday = clone $date;
        $yesterday->modify("-1 day");
        $tomorrow = clone $date;
        $tomorrow->modify("+1 day");

        // if date is in an event
        if ($stmt->rowCount() > 0) {
            $row = $row[0];
            if ($row->getStart() == $row->getEnd()) {
                $this->remove($row->getId());
                http_response_code(200); //Success
                echo json_encode(array("message" => "Event deleted"));
            } else if ($row->getIdCal() == 0){
                 if ($row->getStart() == $date->format("Y-m-d")) {
                    $msg = $this->create(Event::create($tomorrow->format("Y-m-d"), $row->getEnd(),  $row->getDescription()));
                    $this->remove($row->getId());
                } else {
                    // devide the event in 2 events
                    $msg = $this->create(Event::create($row->getStart(),  $yesterday->format("Y-m-d"), $row->getDescription() ));
                    $this->create(Event::create($tomorrow->format("Y-m-d"),  $row->getEnd(),  $row->getDescription()));
                }
            } else{
                http_response_code(500); //Success
                echo json_encode(array("message" => "A distant event already exists"));
            }

        } else {
            $query = "select * from " . $this->tableName . " WHERE start=:dateFor or end=:dateBack";

            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(":dateFor", $this->prepareValue($tomorrow->format("Y-m-d")));
            $stmt->bindValue(":dateBack", $this->prepareValue($yesterday->format("Y-m-d")));

            $stmt->execute();

            $row = $stmt->rowCount();
            if ($row > 0) {
                // if date between 2 events
                if ($row == 2) {
                    $ids = [];
                    $startDate = [];
                    $endDate = [];
                    $desc = [];
                    foreach ($stmt->fetchAll(PDO::FETCH_CLASS,$this->model) as $row) {
                        array_push($ids, $row->getId());
                        array_push($startDate, $row->getStart());
                        array_push($endDate, $row->getEnd());
                        array_push($desc, $row->getDescription());
                    }
                    $start = min($startDate);
                    $idx = array_search($start, $startDate);
                    $end = $endDate[count($endDate) - 1 - $idx];
                    //join events
                    $msg = $this->create(Event::create($start,  $end,  $desc[$idx]));
                    $this->remove($ids[count($ids) - 1 - $idx]);
                } else {
                    $row = $stmt->fetchAll(PDO::FETCH_CLASS,$this->model)[0];
                    if($row->getIdCal()!=0){
                        $msg = $this->create(Event::create($date->format("Y-m-d"), $date->format("Y-m-d"), "WebSite"));
                    }
                    //if date just before event
                    else if ($row->getStart() == $tomorrow->format("Y-m-d")) {
                        //set start date to date
                        $msg = $this->create(Event::create($date->format("Y-m-d"), $row->getEnd(), $row->getDescription()));
                        $this->remove($row->getId());
                    } else
                        // if date just after an event
                        if ($row->getEnd() == $yesterday->format("Y-m-d")) {
                            //extends event to this date
                            $msg = $this->create(Event::create($row->getStart(), $date->format("Y-m-d"),  $row->getDescription()));
                        }
                }
            } else {
                //create a one day event
                $msg = $this->create(Event::create($date->format("Y-m-d"), $date->format("Y-m-d"), "WebSite"));
            }
        }
        if (isset($msg)) {
            echo json_encode(array("message" => $msg));
        }
    }

    /**
     * Create an event.
     *
     * @param stdClass|Event $data
     *
     * @return string
     * @throws Exception
     */
    public function create($data)
    {
        if (!isset($data->start) && !isset($data->end) && !is_a($data, "Calendar\model\Event")) {
            throw new Exception("Unable to create an event. Data is incomplete.", 400);
        } else {
            if (!empty($data->start) &&
                !empty($data->end)) {
                $event = Event::create($data->start, $data->end, "");
                if (!empty($data->description)) {
                    $event->setDescription($data->description);
                }
            } else {
                $event = $data;
            }
        }

        // set event property values
        $cEvent = $this->startCheck($event);
        $msg = "";
        if ($cEvent) {
            if ($cEvent->getEnd() != $event->getEnd()) {
                $cEvent->setEnd($event->getEnd());
                $msg = $this->updateEvent($cEvent);
            }
        } else {
            if(empty($event->getUid())){
               $event->setUid(uniqid());
            }
            $msg = $this->insert($event);
        }
        return $msg;

    }

    /**
     * Check if an event with the same start date is saved in the db
     * @param Event $event
     * @return Event|null
     */

    public function startCheck($event)
    {
        // query to read single record
        $query = "SELECT * FROM " . $this->tableName . "
                WHERE
                    start=:start
                LIMIT
                    0,1";
        // prepare query statement
        $stmt = $this->connection->prepare($query);

        // bind values
        $stmt->bindValue(":start", $this->prepareValue($event->getStart()));

        // execute query
        $stmt->execute();

        // get retrieved row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // check if there are at least 1 match
        if ($stmt->rowCount() > 0) {
            $ev = Event::create($row["start"], $row["end"], $row["description"]);
            $ev->setId($row["id"]);
            return $ev;
        } else {
            return null;
        }
    }

    /**
     * Update an event
     * @param Event $event
     * @return string
     * @throws Exception
     */
    public function updateEvent($event)
    {
        $done = false;
        $data = $event->toArray();
        foreach ($data as $key => $value) {
            if (!empty ($value)) {
                if ($this->update($event->getId(), $key, $value)) {
                    $done = true;
                }
            }
        }
        if (!$done) {
            throw new Exception("Data error", 500);
        }

        http_response_code(200); //Success
        return "Changes made";
    }

    /**
     * Insert an event in the db
     * @param Event $event
     * @return string
     * @throws Exception
     */
    private function insert($event)
    {
        $query = "INSERT INTO " . $this->tableName . "
                        SET start=:start, end=:end, idCal=:idCal, uid=:uid, description=:description";

        // prepare query
        $stmt = $this->connection->prepare($query);

        // bind values
        $stmt->bindValue(":start", $this->prepareValue($event->getStart()));
        $stmt->bindValue(":end", $this->prepareValue($event->getEnd()));
        $stmt->bindValue(":description", $this->prepareValue($event->getDescription()));
        $stmt->bindValue(":uid", $this->prepareValue($event->getUid()));
        $stmt->bindValue(":idCal", $this->prepareValue($event->getIdCal()));

        // execute query
        if ($stmt->execute()) {
            // set response code - 201 Created
            http_response_code(201);
            // tell the user
            return "Event was created.";
        } // if unable to create the user, tell the user
        else {
            print_r($stmt->errorInfo());
            // set response code - 503 service unavailable
            throw new Exception("Unable to create an event", 503);
        }
    }

    /**
     * Get all event from idCal
     * @param $idCal
     * @return Event[]
     * @throws Exception
     */
    public function getCalEvent($idCal){
        // select all query
        $query = "SELECT * FROM " . $this->tableName . " Where idCal=:idCal";

        // prepare query statement
        $stmt = $this->connection->prepare($query);
        $stmt->bindValue(":idCal", $this->prepareValue($idCal));

        // execute query
        $stmt->execute();

        $num = $stmt->rowCount();

        // check if more than 0 record found
        if ($num > 0) {
            return $stmt->fetchAll(PDO::FETCH_CLASS,$this->model);
        }
        return array();
}

    /**
     * @param Event $event
     * @param Event[] $eventArray
     * @return bool
     */
    public function eventInArray($event, $eventArray)
    {
        if(!in_array($event, $eventArray)){
            foreach ($eventArray as $ev){
                if ($ev->getStart() == $event->getStart() && $ev->getEnd()==$event->getEnd()){
                    $ev->setId($event->getId());
                    return true;
                }
            }
            return false;
        }else{
            return true;
        }
    }
}