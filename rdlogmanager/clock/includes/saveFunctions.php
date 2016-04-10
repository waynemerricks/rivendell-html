<?php

  

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

    $stmt = NULL;

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

  /**
   * Renames a clocks CLK and RULES table
   * @param $PDO PDO Connection to use
   * @param $oldName Old/Current clock name
   * @param $newName Name you want to rename to
   * dies on error
   */
  function renameClockTables($PDO, $oldName, $newName){

    //Rename _CLK
    $oldTableName = str_replace(' ', '_', $oldName);
    $newTableName = str_replace(' ', '_', $newName);
    $oldTableName = escapeTableName($PDO, $oldTableName . '_CLK');
    $newTableName = escapeTableName($PDO, $newTableName . '_CLK');
    $sql = 'ALTER TABLE ' . $oldTableName . ' RENAME TO ' . $newTableName;

    if($PDO->query($sql) === FALSE)
      die('Error renaming clock table from ' . $oldName . ' to ' . $newName);

    //Rename _RULES
    $oldTableName = str_replace(' ', '_', $oldName);
    $newTableName = str_replace(' ', '_', $newName);
    $oldTableName = escapeTableName($PDO, $oldTableName . '_RULES');
    $newTableName = escapeTableName($PDO, $newTableName . '_RULES');

    $sql = 'ALTER TABLE ' . $oldTableName . ' RENAME TO ' . $newTableName;

    if($PDO->query($sql) === FALSE)
      die('Error renaming clock rules table from ' . $oldName . ' to ' . $newName);

  }

  /**
   * Renames a given clock in the CLOCKS table
   * @param $PDO PDO Connection to use
   * @param $oldName Old/Current clock name
   * @param $newName Name you want to rename to
   * dies on error
   */
  function renameClock($PDO, $oldName, $newName){

    $sql = 'UPDATE `CLOCKS` SET `NAME` = :newName WHERE `NAME` = :oldName';
    $stmt = $PDO->prepare($sql);
    $stmt->bindParam(':newName', $newName);
    $stmt->bindParam(':oldName', $oldName);

    if($stmt->execute() === FALSE)
      die('Error renaming clock in CLOCKS table: ' . $oldName . ' to ' . $newName);

    $stmt = NULL;

  }

  /**
   * Renames a given clock in the CLOCK_PERMS table
   * @param $PDO PDO Connection to use
   * @param $oldName Old/Current clock name
   * @param $newName Name you want to rename to
   * dies on error
   */
  function renameClockPerms($PDO, $oldName, $newName){

    $sql = 'UPDATE `CLOCK_PERMS` SET `CLOCK_NAME` = :newName
            WHERE `CLOCK_NAME` = :oldName';
    $stmt = $PDO->prepare($sql);
    $stmt->bindParam(':newName', $newName);
    $stmt->bindParam(':oldName', $oldName);

    if($stmt->execute() === FALSE)
      die('Error renaming clock in CLOCK_PERMS table: ' . $oldName . ' to ' . $newName);

    $stmt = NULL;

  }

  function renameClockInGrids($PDO, $oldName, $newName){

    /* Can only really brute force through all 167 clock columns
     * changes coming in Riv github means this won't be an issue (CLOCK_SVC table)
     * in newer versions than 2.10.3 */
    for($i = 0; $i < 168; $i++){

      $sql = 'UPDATE `SERVICES` SET `CLOCK' . $i . '` = :newName
              WHERE `CLOCK' . $i . '` = :oldName';

      $stmt = $PDO->prepare($sql);
      $stmt->bindParam(':newName', $newName);
      $stmt->bindParam(':oldName', $oldName);

      if($stmt->execute() === FALSE)
        die('Failed to rename SERVICE Table CLOCK' . $i . ' from ' . $oldName . ' to '
            . $newName);

      $stmt = NULL;

    }//End Clocks For Loop

  }

  /**
   * Copies a given clock table to a new table, includes the data this contains
   * @param $PDO PDO Connection to use
   * @param $sourceName Name of Clock to copy
   * @param $copyName Name of Copy
   * dies on error
   */
  function copyClockTable($PDO, $sourceName, $copyName){

    $sourceTableName = str_replace(' ', '_', $sourceName);
    $sourceTableName = escapeTableName($PDO, $sourceTableName . '_CLK');
    $copyTableName = str_replace(' ', '_', $copyName);
    $copyTableName = escapeTableName($PDO, $copyTableName . '_CLK');

    //Create tables with indexes etc
    $sql = 'CREATE TABLE ' . $copyTableName . ' LIKE ' . $sourceTableName;

    if($PDO->query($sql) === FALSE)
      die('Error copying clock table from ' . $sourceTableName . ' to ' . $copyTableName);

    //Insert records from source to copy
    $sql = 'INSERT ' . $copyTableName . ' SELECT * FROM ' . $sourceTableName;

    if($PDO->query($sql) === FALSE)
      die('Error copying clock data from ' . $sourceTableName . ' to ' . $copyTableName);

  }

  /**
   * Copies a given clocks rules to a new table
   * @param $PDO PDO Connection to use
   * @param $sourceName Name of Clock to copy
   * @param $copyName Name of Copy
   * dies on error
   */
  function copyClockRulesTable($PDO, $sourceName, $copyName){

    $sourceTableName = str_replace(' ', '_', $sourceName);
    $sourceTableName = escapeTableName($PDO, $sourceTableName . '_RULES');
    $copyTableName = str_replace(' ', '_', $copyName);
    $copyTableName = escapeTableName($PDO, $copyTableName . '_RULES');

    //Create tables with indexes etc
    $sql = 'CREATE TABLE ' . $copyTableName . ' LIKE ' . $sourceTableName;

    if($PDO->query($sql) === FALSE)
      die('Error copying clock rules table from ' . $sourceTableName . ' to '
          . $copyTableName);

    //Insert records from source to copy
    $sql = 'INSERT ' . $copyTableName . ' SELECT * FROM ' . $sourceTableName;

    if($PDO->query($sql) === FALSE)
      die('Error copying clock rules from ' . $sourceTableName . ' to ' . $copyTableName);

  }

  /**
   * Adds a clock to the CLOCK_PERMS table
   * @param $name CLOCK_NAME
   * @param $service SERVICE_NAME
   * Dies on error
   */
  function addClockPerms($PDO, $name, $service){

    $sql = 'INSERT INTO `CLOCK_PERMS` (`CLOCK_NAME`, `SERVICE_NAME`)
            VALUES (:name, :service)';

    $stmt = $PDO->prepare($sql);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':service', $service);

    if($stmt->execute() === FALSE)
      die('Error inserting clock into CLOCK_PERMS table: ' . $name);

    $stmt = NULL;

  }

  /**
   * Updates a given clocks short name/code to a new code
   * Check with code exists before using this
   * @param $PDO PDO connection to use
   * @param $clockName Name of clock to update
   * @param $newCode Code to change clock too
   * dies on error
   */
  function updateClockCode($PDO, $clockName, $newCode){

    $sql = 'UPDATE `CLOCKS` SET `SHORT_NAME` = :newCode WHERE `NAME` = :clockName';
    $stmt = $PDO->prepare($sql);
    $stmt->bindParam(':newCode', $newCode);
    $stmt->bindParam(':clockName', $clockName);

    if($stmt->execute() === FALSE)
      die('Error updating CLOCK ' . $clockName . ' code to ' . $newCode);

    $stmt = NULL;

  }

?>
