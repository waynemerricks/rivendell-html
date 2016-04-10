<?php

  include('../../config/database.php');
  include('includes/commonFunctions.php');
  include('includes/deleteFunctions.php');

  if(isset($_POST)){

    $PDO = getDatabaseConnection();

    //CHECK EXISTS
    if(!clockExists($PDO, $_POST['name']))
       die('Clock ' . $_POST['name'] . ' does not exist, can\'t delete');

    //die('debug we don\'t want to do this right now');
    //DELETE TABLE + RULES
    deleteClockTables($PDO, $_POST['name']);
    
    //DELETE FROM CLOCKS AND CLOCK_PERMS
    deleteClock($PDO, $_POST['name']);
    
    //DELETE FROM GRIDS
    renameClockInGrids($PDO, $_POST['name'], '');

    echo 'Clock ' . $_POST['name'] . ' has been deleted';
       
  }

?>
