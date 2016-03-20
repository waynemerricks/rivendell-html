<?php

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

?>
