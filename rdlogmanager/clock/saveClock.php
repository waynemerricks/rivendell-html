<?php

  include('../../config/database.php');
  include('includes/saveFunctions.php');

  if(isset($_POST['name'], $_POST['shortName'], $_POST['originalName'],
        $_POST['originalShortName'], $_POST['mode'], $_POST['events'], $_POST['colour'])){

    $PDO = getDatabaseConnection();

    if($_POST['originalName'] == ''){
      echo 'SAVE NEW CLOCK';
      //CHECK NAME EXISTS (IT SHOULDN'T)
      if(clockExists($PDO, $_POST['name']))
        die('Clock ' . $_POST['name'] . ' already exists, change it and try again');

      //AND CODE
      if(clockCodeExists($PDO, $_POST['shortName']))
        die('Clock Code ' . $_POST['shortName']
            . ' already exists, change it and try again');

      //ADD CLOCK TO CLOCKS TABLE: TODO Artist Sep + Remarks
      addClock($PDO, $_POST['name'], $_POST['shortName'], $_POST['colour'], 5, '');

      //CREATE NEW TABLE (CLOCK + RULES)
      createClockTables($PDO, $_POST['name']);

      //SAVE EVENTS TO NEW TABLE
      saveEvents($PDO, $_POST['name'], $_POST['events']);

    }else if($_POST['mode'] == 'save'){

      if($_POST['originalName'] != $_POST['name']){
        echo 'RENAME';
        //CHECK NAME EXISTS (IT SHOULDN'T)
        //CHECK ORIGINAL EXISTS (IT SHOULD)
        //SAVE EVENTS TO ORIGINAL CLOCK TABLE
        //RENAME TABLES (CLOCK AND CLOCK RULES)
        //RENAME IN CLOCKS
        //RENAME IN GRIDS
      }else{
        echo 'SAVE';
        //CHECK NAME EXISTS (IT SHOULD)
        //SAVE EVENTS TO CLOCK TABLE
      }

    }else if($_POST['mode'] == 'saveas'){

      if($_POST['originalName'] != $_POST['name']){
        echo 'SAVE COPY';
        //CHECK NAME EXISTS (IT SHOULDN'T)
        //CHECK ORIGINAL EXISTS (IT SHOULD)
        //COPY ORIGINAL TABLE
        //COPY ORIGINAL RULES
        //SAVE EVENTS TO NEW TABLE
      }else{
        echo 'CAN\'T SAVE AS SAME CLOCK';
      }

    }

    $PDO = NULL;

  }else
    echo 'Incorrect usage';

?>
