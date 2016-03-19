<?php

  /**
   * Gets the clocks for the Given Rivendell service
   * @param $PDO: PDO Connection to use
   * @param $service: Service to lookup
   * @return array of clocks (name, short_name, color)
   */
  function getRivendellClocks($PDO, $service){

    $clocks = array();

    $sql = 'SELECT `NAME`, `SHORT_NAME`, `COLOR` FROM `CLOCKS`';

    $results = $PDO->query($sql);
    $results->setFetchMode(PDO::FETCH_ASSOC);

    while($row = $results->fetch()){

      $clocks[$row['NAME']] = $row;

    }

    $reults = NULL;

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

  /**
   * Gets the full grid for the specified service
   * @param $PDO: PDO Connection to use
   * @param $service: Service Name to get
   * @return array of service including all 168 clocks
   */
  function getGrid($PDO, $service){

    $grid = NULL;

    $sql = 'SELECT * FROM `SERVICES` WHERE `NAME` = :serviceName';

    $stmt = $PDO->prepare($sql);
    $stmt->bindParam(':serviceName', $service);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_ASSOC);

    while($row = $stmt->fetch()){

      $grid = $row;

    }

    $stmt = NULL;

    return $grid;

  }

  //TODO
  function updateServiceGrid($PDO, $service, $grid){}

?>
