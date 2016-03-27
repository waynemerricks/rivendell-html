<?php

  /**
   * Checks if a given clock exists
   * @param $PDO PDO Connection to use
   * @param $name Name of clock to check
   * @return true if exists
   */
  function clockExists($PDO, $name){

    $exists = false;

    $sql = 'SELECT `NAME` AS `CLOCK_COUNT` FROM `CLOCKS` WHERE `NAME` = ?';

    $stmt = $PDO->prepare($sql);
    $stmt->bindParam(1, $name);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);

    if($stmt->rowCount() > 0)
      $exists = true;

    $stmt = NULL;

    return $exists;

  }

  /**
   * Adds a clock to the CLOCKS table
   * @param $name Name of Clock
   * @param $shortName Code/Short name for this clock
   * @param $colour HTML hex colour for this clock
   * @param $artistSeparation Number of artists to separate by
   * @param $remarks Comments/Remarks for this clock
   */
  function addClock($PDO, $name, $shortName, $colour, $artistSeparation, $remarks){

    if(substr($colour, 0, 1) != '#')
      $colour = '#' . $colour;

    $sql = 'INSERT INTO `CLOCKS` (`NAME`, `SHORT_NAME`, `ARTISTSEP`, `COLOR`, `REMARKS`)
            VALUES (:name, :shortName, :artistSeparation, :colour, :remarks)';

    $stmt = $PDO->prepare($sql);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':shortName', $shortName);
    $stmt->bindParam(':artistSeparation', $artistSeparation);
    $stmt->bindParam(':colour', $colour);
    $stmt->bindParam(':remarks', $remarks);

    if($stmt->execute() === FALSE || $stmt->rowCount() != 1)
      die('Error inserting ' . $name . ' into CLOCKS table');

  }

  /**
   * Checks if a given clock exists
   * @param $PDO PDO Connection to use
   * @param $name Name of clock code to check
   * @return true if exists
   */
  function clockCodeExists($PDO, $name){

    $exists = false;

    $sql = 'SELECT `NAME` AS `CLOCK_COUNT` FROM `CLOCKS` WHERE `SHORT_NAME` = ?';

    $stmt = $PDO->prepare($sql);
    $stmt->bindParam(1, $name);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);

    if($stmt->rowCount() > 0)
      $exists = true;

    $stmt = NULL;

    return $exists;

  }

  /**
   * Return a quoted table name but with ` instead of default ' from PDO->quote()
   * @param $PDO PDO connection to use
   * @param $name Name of table to escape
   * @return `$name_properly_escaped?`;
   */
  function escapeTableName($PDO, $name){

    /* PDO kind of sucks with table names you can't use statements and the quote method
     * returns as 'TABLE_NAME' and you can't use apostrophes in CREATE 'TABLE_NAME'
     * So we have to remove the first and last ' and replace with `.
     */
    $name = $PDO->quote($name);

    $name = substr($name, 1);
    $name = substr($name, 0, -1);

    return '`' . $name . '`';

  }

  /**
   * Creates this clocks _CLK and _RULES table
   * Correct as of Riv 2.10.3, not compatible with github code switch to CLOCKS_METADATA
   * @param $PDO PDO Connection to use
   * @param $name name of clock to create (will trim whitespace)
   * Will die on error (may change this later)
   */
  function createClockTables($PDO, $name){

    //Create Clock Table _CLK
    $name = str_replace(' ', '_', trim($name));//substitute spaces and trim whitespace
    $tableName = escapeTableName($PDO, $name . '_CLK'); //sanitise name, can't use statements

    $sql = 'CREATE TABLE ' . $tableName . ' (
              ID int unsigned auto_increment not null primary key,
              EVENT_NAME char(64) not null,
              START_TIME int not null,
              LENGTH int not null,
              INDEX EVENT_NAME_IDX (EVENT_NAME)
            )';

    if($PDO->query($sql) === FALSE)
      die('Error creating Clock Table: ' . $tableName);

    //Create Rules Table _RULES
    $tableName = escapeTableName($PDO, $name . '_RULES');
    $sql = 'CREATE TABLE ' . $tableName . ' (
              CODE varchar(10) not null primary key,
              MAX_ROW int unsigned,
              MIN_WAIT int unsigned,
              NOT_AFTER varchar(10),
              OR_AFTER varchar(10),
              OR_AFTER_II varchar(10)
            )';

    if($PDO->query($sql) === FALSE)
      die('Error creating Clock Table: ' . $tableName);

  }

  /**
   * Saves Events to the given Clock table
   * @param $PDO PDO connection to use
   * @param $name Name of clock these events belong to
   * @param $events array of events to use for this clock
   *   [EVENT_NAME, START_TIME, LENGTH]
   * v2.10.3
   * Will die on error
   */
  function saveEvents($PDO, $name, $events){

    if(sizeof($events) == 0)
      die('No events to save');

    //Remove the old events
    removeClockEvents($PDO, $name);

    //Insert new events
    $name = str_replace(' ', '_', $name);
    $tableName = escapeTableName($PDO, $name . '_CLK');

    $sql = 'INSERT INTO ' . $tableName . ' (`EVENT_NAME`, `START_TIME`, `LENGTH`)
            VALUES ';

    foreach($events as $event)
      $sql .= '(?, ?, ?),';

    //Remove trailing ,
    $sql = substr($sql, 0, -1);

    $stmt = $PDO->prepare($sql);

    $paramNo = 1;

    foreach($events as $event){

      $stmt->bindParam($paramNo, $event[0]);//NAME
      $stmt->bindParam($paramNo + 1, $event[1]);//START
      $stmt->bindParam($paramNo + 2, $event[2]);//LENGTH
      $paramNo += 3;

    }

    if($stmt->execute() === FALSE)
      die('Error inserting clock events: ' . $tableName);

    if($stmt->rowCount() != sizeof($events))
      die('Error, insert count does not match number of events was ' . $stmt->rowCount
          . ' should have been ' . sizeof($events));

    $stmt = NULL;

  }

  /**
   * Emptys the given clock of all events
   * Riv seems to delete all events then insert new ones upon save
   * @param $PDO PDO connection to use
   * @param $name Name of clock to remove events
   * Will die on error
   */
  function removeClockEvents($PDO, $name){

    $name = str_replace(' ', '_', $name);
    $tableName = escapeTableName($PDO, $name . '_CLK');
    $sql = 'DELETE FROM ' . $tableName;

    if($PDO->query($sql) === FALSE)
      die('Error removing old events from clock: ' . $tableName);

  }

  function renameClock($PDO, $oldName, $newName){

    //TODO

  }

  function copyClock($PDO, $sourceName, $copyName){

    //TODO

  }

?>
