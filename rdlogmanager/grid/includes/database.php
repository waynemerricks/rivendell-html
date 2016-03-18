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

      $clock = array();
      $clock['name'] = $row['NAME'];
      $clock['short_name'] = $row['SHORT_NAME'];
      $clock['color'] = $row['COLOR'];
      $clocks[] = $clock;

    }

    $reults = NULL;

    return $clocks;

  }

  //TODO
  function getServiceNames($PDO){}

  function getServices($PDO){}

  function getGrid($PDO, $service){}

  function updateServiceGrid($PDO, $service, $grid){}

?>
