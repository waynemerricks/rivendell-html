<?php

  include('../../config/database.php');

  //TODO Protect from rogue POST events hitting this page
  if(isset($_POST['service'], $_POST['grid'])){

    //Formulate SQL
    $sql = 'UPDATE `SERVICES` SET ';

    for($i = 0; $i < 168; $i++)
      $sql .= '`CLOCK' . $i . '` = ?, ';

    //Remove trailing ,
    $sql = substr($sql, 0, -2);

    $sql .= ' WHERE `NAME` = ?';

    //Put data into 1 array (all clocks then service name)
    $data = $_POST['grid'];
    $data[] = $_POST['service'];

    //Connect to DB and prepare statement
    $PDO = getDatabaseConnection();
    $stmt = $PDO->prepare($sql);

    if($stmt->execute($data))
      echo 'Successfully saved ' . $_POST['service'] . ' grid.';
    else die('Error executing statement (' . $stmt->errorCode() . ') '
          . $stmt->errorInfo());

  }

?>
