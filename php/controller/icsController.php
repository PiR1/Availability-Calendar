<?php
/**
 * Copyright (C) PiR1, Inc - All Rights Reserved
 *    Apache License
 *    Version 2.0, January 2004
 *    http://www.apache.org/licenses/
 *    See Licence file
 *
 * @file      icsController.php
 * @author    PiR1
 * @date     25/05/2020 23:25
 */

namespace Calendar\controller;

use Calendar\config\DBClass;
use Calendar\model\Event;
use Calendar\model\Ical;
use Calendar\utils\iCalEasyReader;
use DateTime;
use Exception;

class icsController extends controller
{

    /**
     * icsController constructor.
     * @param $connection
     * @throws Exception
     */
    public function __construct($connection)
    {
        parent::__construct($connection, "ics", "\Calendar\model\ical");
    }


    /**
     * Read all ics calendars.
     * @return Ical[]|null
     * @throws Exception
     */
    public function getAll()
    {
      $icals=$this->fetchAll();
      if(sizeof($icals)>0){
          $ret=array();
          foreach ($icals as $item) {
              array_push($ret, $item->toArray());
          }
          echo json_encode($ret);

        } else {
            return null;
        }
    }


    /**
     * Update an ical
     * @param $data
     * @throws Exception
     */
    public function updateIcal($data)
    {
        if(!empty($data->url) && !empty($data->type)){
            if(!empty($data->id)){
                $u1=parent::update($data->id, "url", $data->url);
                $u2=parent::update($data->id, "type", $data->type);
                if($u1 && $u2){
                    http_response_code(200); //Success
                    echo json_encode(array("message"=>"Changes made"));
                }else{
                    throw new Exception("Unable to update the ical", 503);
                }
            }else
            {
                $ical = Ical::create($data->url, $data->type);
                $this->insert($ical);
            }

        }
    }

    /**
     * Create an ical
     * @param Ical $ical
     * @return string
     * @throws Exception
     */
    private function insert($ical)
    {
        $query = "INSERT INTO " . $this->tableName . "
                        SET url=:url, type=:type";

        // prepare query
        $stmt = $this->connection->prepare($query);

        // bind values
        $stmt->bindValue(":url", $this->prepareValue($ical->getUrl()));
        $stmt->bindValue(":type", $this->prepareValue($ical->getType()));

        // execute query
        if ($stmt->execute()) {
            $this->connection->lastInsertId();
            // set response code - 201 Created
            http_response_code(201);
            // tell the user
            echo json_encode(array("id"=>$this->connection->lastInsertId(),"message"=>"Ical was added."));
        } // if unable to create the user, tell the user
        else {
            // set response code - 503 service unavailable
            throw new Exception("Unable to create an ical", 503);
        }
    }

    /**
     * Delete an ical
     * @param $data
     * @throws Exception
     */
    public function delete($data){
        if (!empty($data) && is_int($data)){
            if(parent::remove($data)){
                http_response_code(200);
                // tell the user
                echo json_encode(array("message"=>"Ical deleted."));
            }
        }
        else{
            throw new Exception("Unable to delete this ical", 503);
        }
    }

    /**
     * Save ics event to the DB
     * @throws Exception
     */
    public function icsToMysql(){
        $calendars = $this->fetchAll();
        $eventCtrl = new eventController((new DBClass())->getConnection());
        foreach ($calendars as $calendar) {
            $ical = new iCalEasyReader();
            $ics = $ical->load(file_get_contents($calendar->getUrl()));
            $calEvents = $eventCtrl->getCalEvent($calendar->getId());
            foreach ($ics["VEVENT"] as $event) {
                $date_start = $event["DTSTART"]["value"];
                $date_end = $event["DTEND"]["value"];
                $start = new DateTime(date("Y-m-d", strtotime($date_start)));
                $end = new DateTime(date("Y-m-d", strtotime($date_end)));
                $ev = Event::create($start->format("Y-m-d"), $end->format("Y-m-d"),$event['SUMMARY']);
                if(!$eventCtrl->eventInArray($ev, $calEvents) ) {
                    $ev->setIdCal($calendar->getId());
                    $ev->setUid($event["UID"]);
                    if (!$eventCtrl->check($ev)) {
                        $eventCtrl->create($ev);
                    }
                }else{
                    unset($calEvents[array_search($ev, $calEvents)]);
                }
            }
            foreach ($calEvents as $event){
                $eventCtrl->remove($event->getId());
            }
        }
    }

    /**
     * Generate an ics calendar from db
     * @throws Exception
     */
    public function mysqlToIcs(){
        //get modifs
        $this->icsToMysql();

        $eventCtrl = new eventController((new DBClass())->getConnection());
        $events = $eventCtrl->FetchAll();

        $ics_data = "BEGIN:VCALENDAR\r\n";
        $ics_data .= "VERSION:2.0\r\n";
        $ics_data .= "CALSCALE:GREGORIAN\r\n";
        $ics_data .= "PRODID:PHP\r\n";
        $ics_data .= "METHOD:PUBLISH\r\n";
        $ics_data .= "X-WR-CALNAME:Schedule\r\n";

        # Change the timezone if needed
        $ics_data .= "X-WR-TIMEZONE:Europe/Paris\r\n";

        foreach($events as $event) {
            $start_time = "160000";
            $end_time = "120000";


            $start_date = str_replace("-", "", $event->getStart());
            $end_date = str_replace("-", "", $event->getEnd());

            # Change TimeZone if needed
            $ics_data .= "BEGIN:VEVENT\r\n";
            $ics_data .= "DTSTART:" . $start_date . "T" . $start_time . "\r\n";
            $ics_data .= "DTEND:" . $end_date . "T" . $end_time . "\r\n";
            $ics_data .= "DTSTAMP:" . date('Ymd') . "T" . date('His') . "Z\r\n";
            //$ics_data .= "LOCATION:" . $location . "\r\n";
            //$ics_data .= "DESCRIPTION:" . $description . "\r\n";
            $ics_data .= "LOCATION: \r\n";
            $ics_data .= "DESCRIPTION: \r\n";
            $ics_data .= "SUMMARY:" . $event->getDescription() . "\r\n";
            $ics_data .= "UID:" . $event->getUid() . "\r\n";
            $ics_data .= "SEQUENCE:0\r\n";
            $ics_data .= "END:VEVENT\r\n";
        }
        $ics_data .= "END:VCALENDAR\r\n";


        # Download the File
        header('Content-Type: application/ics');
        header('Content-Disposition: attachment; filename="Calendar.ics"');
        header('Content-Length: ' . mb_strlen($ics_data, '8bit'));
        echo $ics_data;
    }
}