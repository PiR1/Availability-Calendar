<?php

use Cassandra\Date;

include "model/event.php";
include "controller.php";

class eventController extends controller
{

    /**
     * eventController constructor.
     * @param $connection
     * @throws Exception
     */
    public function __construct($connection)
    {
        parent::__construct($connection, "events");
    }

    /**
     * Read all events.
     *
     * @throws Exception
     */
    public function getAll()
    {

        // select all query
        $query = "SELECT * FROM " . $this->tableName;

        // prepare query statement
        $stmt = $this->connection->prepare($query);

        // execute query
        $stmt->execute();

        $num = $stmt->rowCount();

        // check if more than 0 record found
        if ($num > 0) {

            // meridiens array
            $event_arr = array();
            // $event_arr["records"] = array();

            // retrieve our table contents
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // extract row, transform $row['key']=value to $key=value
                extract($row);

                /** @var date $start */
                /** @var date $end */
                $event = array(
                    "start" => $start,
                    "end" => $end
                );

                // array_push($event_arr["records"], $event);
                array_push($event_arr, $event);
            }

            // set response code - 200 OK
            http_response_code(200);

            // show meridiens data in json format
            echo json_encode($event_arr);

        } else {
            throw new Exception("No event found", 404);
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
        $stmt->bindParam(":start", htmlspecialchars(strip_tags($event->getStart())));
        $stmt->bindParam(":end", htmlspecialchars(strip_tags($event->getEnd())));

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

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $yesterday = clone $date;
        $yesterday->modify("-1 day");
        $tomorrow = clone $date;
        $tomorrow->modify("+1 day");

        // if date is in an event
        if ($stmt->rowCount() > 0) {
            if ($row["start"] == $row["end"]) {
                $this->remove($row["id"]);
                http_response_code(200); //Success
                echo json_encode(array("message" => "Event deleted"));
            } else if ($row["end"] == $date->format("Y-m-d")) {
                $msg = $this->create((object)["start" => $row["start"], "end" => $yesterday->format("Y-m-d")]);
            } else if ($row["start"] == $date->format("Y-m-d")) {
                $msg = $this->create((object)["start" => $tomorrow->format("Y-m-d"), "end" => $row["end"]]);
                $this->remove($row["id"]);
            } else {
                // devide the event in 2 events
                $msg = $this->create((object)["start" => $row["start"], "end" => $yesterday->format("Y-m-d")]);
                $this->create((object)["start" => $tomorrow->format("Y-m-d"), "end" => $row["end"]]);
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
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        array_push($ids, $row["id"]);
                        array_push($startDate, $row["start"]);
                        array_push($endDate, $row["end"]);
                        array_push($desc, $row["description"]);
                    }
                    $start = min($startDate);
                    $idx = array_search($start, $startDate);
                    $end = $endDate[count($endDate) - 1 - $idx];
                    //join events
                    $msg = $this->create((object)["start" => $start, "end" => $end, "description" => $desc[$idx]]);
                    $this->remove($ids[count($ids) - 1 - $idx]);
                } else {
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    //if date just before event
                    if ($row["start"] == $tomorrow->format("Y-m-d")) {
                        //set start date to date
                        $msg = $this->create((object)["start" => $date->format("Y-m-d"), "end" => $row["end"], "description" => $row["description"]]);
                        $this->remove($row["id"]);
                    } else
                        // if date just after an event
                        if ($row["end"] == $yesterday->format("Y-m-d")) {
                            //extends event to this date
                            $msg = $this->create((object)["start" => $row["start"], "end" => $date->format("Y-m-d")]);
                        }
                }
            } else {
                //create a one day event
                $msg = $this->create((object)["start" => $date->format("Y-m-d"), "end" => $date->format("Y-m-d")]);
            }
        }
        if (isset($msg)) {
            echo json_encode(array("message" => $msg));
        }
    }

    /**
     * Create an event.
     *
     * @param stdClass $data
     *
     * @return string
     * @throws Exception
     */
    public function create($data)
    {
        // make sure data is not empty
        if (
            !empty($data->start) &&
            !empty($data->end)
        ) {

            $event = new Event($data->start, $data->end, "");
            if (!empty($data->description)) {
                $event->setDescription($data->description);
            }

            // set user property values
            $cEvent = $this->startCheck($event);
            $msg = "";
            if ($cEvent) {
                if ($cEvent->getEnd() != $event->getEnd()) {
                    $cEvent->setEnd($event->getEnd());
                    $msg = $this->updateEvent($cEvent);
                }
            } else {
                $msg = $this->insert($event);
            }
            return $msg;


        } // tell the user data is incomplete
        else {
            throw new Exception("Unable to create an event. Data is incomplete.", 400);
        }

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
            $ev = new Event($row["start"], $row["end"], $row["description"]);
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
                        SET start=:start, end=:end, description=:description";

        // prepare query
        $stmt = $this->connection->prepare($query);

        // bind values
        $stmt->bindValue(":start", $this->prepareValue($event->getStart()));
        $stmt->bindValue(":end", $this->prepareValue($event->getEnd()));
        $stmt->bindValue(":description", $this->prepareValue($event->getDescription()));

        // execute query
        if ($stmt->execute()) {
            // set response code - 201 Created
            http_response_code(201);
            // tell the user
            return "Event was created.";
        } // if unable to create the user, tell the user
        else {
            // set response code - 503 service unavailable
            throw new Exception("Unable to create an event", 503);
        }
    }
}