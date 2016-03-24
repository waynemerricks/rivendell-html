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
      $events[$row['NAME']] = $row;

    $results = NULL;

    return $events;

  }

  /**
   * Gets the events for a clock
   * @param $PDO PDO Connection to use
   * @param $clockName name of the clock you want (relates to the table name)
   * return array of events for clock [ID, EVENT_NAME, START_TIME, LENGTH]
   */
  function getClock($PDO, $clocks, $clockName){

    $events = array();

    /* We can't bind a table name so we have to check against known clocks
     * and escape appropriately */
    if(isValidClock($clocks, $clockName)){

      $tableName = str_replace(' ', '_', $clockName) . '_CLK';
      $sql = 'SELECT * FROM `' . $tableName . '`  ORDER BY `START_TIME` ASC';

      $results = $PDO->query($sql);
      $results->setFetchMode(PDO::FETCH_ASSOC);

      while($row = $results->fetch())
        $events[] = $row;

      $results = NULL;

    }//End valid clock check

    return $events;

  }

  /**
   * Compares the clocks array to the name given
   * We can't bindParam table names so we have to check this name against
   * known clocks that couldn't be user manipulated in the same way
   * @param $clocks array of clocks from clock pallete
   * @param $name name to check for existance in $clocks[?]['NAME'];
   * @return true if it exists, false if not
   */
  function isValidClock($clocks, $name){

    $valid = FALSE;

    if(isset($clocks[$name]))
      $valid = TRUE;

    return $valid;

  }

  /**
   * Converts millis to MM:SS.s
   * @param $millis millis to convert
   * @return millis in MM:SS.s format e.g. 30000 = 00:30
   */
  function getDuration($millis){

    //minutes
    $mins = 0;

    if($millis >= 60000)
      $mins = (int)($millis / 60000);

    while(strlen($mins) < 2)
      $mins = '0' . $mins;

    $millis = $millis - ($mins * 60000);

    //seconds
    $secs = 0;

    if($millis >= 1000)
      $secs = (int)($millis / 1000);

    while(strlen($secs) < 2)
      $secs = '0' . $secs;

    $millis = $millis - ($secs * 1000);


    $time = $mins . ':' . $secs;

    if($millis > 0)
      $time .= '.' . (int)($millis / 100);

    return $time;

  }

?>
