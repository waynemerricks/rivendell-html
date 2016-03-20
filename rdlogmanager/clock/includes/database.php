<?php

  /**
   * Gets the Events for the associated service
   * @param $PDO PDO connection to use
   * @param $serviceName Service to find events for
   * @return array of events matching Riv Events table
   */
  function getRivendellEvents($PDO, $serviceName){

    $events = array();

    $sql = 'SELECT * FROM `EVENTS` ORDER BY `NAME` ASC';

    $results = $PDO->query($sql);
    $results->setFetchMode(PDO::FETCH_ASSOC);

    while($row = $results->fetch())
      $events[] = $row;

    $results = NULL;

    return $events;

  }

?>
