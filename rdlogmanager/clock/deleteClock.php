<?php

  include('../../config/database.php');
  include('includes/commonFunctions.php');
  include('includes/deleteFunctions.php');

  if(isset($_POST)){

    $PDO = getDatabaseConnection();

    var_dump($_POST);
    //CHECK EXISTS
    if(!clockExists($PDO, $_POST['name']))
       die('Clock ' . $_POST['name'] . ' does not exist, can\'t delete');

    //DELETE TABLE + RULES
    //DELETE FROM CLOCKS AND CLOCK_PERMS
    //DELETE FROM GRIDS
    die('debug we don\'t want to do this right now');
    renameClockInGrids($PDO, $_POST['name'], '');

    echo 'Clock ' . $_POST['name'] . ' has been deleted';
       
  }

?>
