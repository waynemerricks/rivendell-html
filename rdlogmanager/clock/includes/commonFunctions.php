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
   * Updates the SERVICES table to rename a given clock in the grid
   * @param $PDO PDO Connection
   * @param $oldName Original name of clock
   * @param $newName Name to change it to
   * NB: Renaming to '' is how Rivendell deletes from the Grid
   */
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

?>