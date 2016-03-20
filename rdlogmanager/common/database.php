<?php

  /**
   * Gets the clocks for the Given Rivendell service
   * @param $PDO: PDO Connection to use
   * @param $service: Service to lookup
   * @return array of clocks (name, short_name, color)
   */
  function getRivendellClocks($PDO, $service){

    $clocks = array();

    //Get the clocks from the perms table
    $sql = 'SELECT `CLOCK_NAME` FROM `CLOCK_PERMS`
            WHERE `SERVICE_NAME` = :service
            ORDER BY `CLOCK_NAME` ASC';

    $stmt = $PDO->prepare($sql);
    $stmt->bindParam(':service', $service);
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $stmt->execute();

    while($row = $stmt->fetch())
      $clocks[] = $row['CLOCK_NAME'];

    $stmt = NULL;

    /* Format into a csv and requery CLOCK table for clocks we have
     * permission to see.
     */
    $clockNames = join(',', array_fill(0, count($clocks), '?'));

    $sql = 'SELECT `NAME`, `SHORT_NAME`, `COLOR` FROM `CLOCKS`
            WHERE `NAME` IN (' . $clockNames . ')';

    $stmt = $PDO->prepare($sql);
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    $stmt->execute($clocks);

    $clocks = array();

    while($row = $stmt->fetch())
      $clocks[$row['NAME']] = $row;

    $stmt = NULL;

    return $clocks;

  }

  /**
   * Gets the service names in the RIV DB
   * @param $PDO: PDO Connection to use
   * @return array of service names
   */
  function getServiceNames($PDO){

    $services = array();

    $sql = 'SELECT `NAME` FROM `SERVICES` ORDER BY `NAME` ASC';

    $results = $PDO->query($sql);
    $results->setFetchMode(PDO::FETCH_ASSOC);

    while($row = $results->fetch()){

      foreach($row as $field)
        $services[] = $field;

    }

    $results = NULL;

    return $services;

  }

?>
