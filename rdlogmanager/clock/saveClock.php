<?php

  include('../../config/database.php');
  include('includes/saveFunctions.php');

  if(isset($_POST['name'], $_POST['shortName'], $_POST['originalName'],
        $_POST['originalShortName'], $_POST['mode'], $_POST['events'], $_POST['colour'],
        $_POST['service'])){

    $PDO = getDatabaseConnection();

    if($_POST['originalName'] == ''){

      //CHECK NAME EXISTS (IT SHOULDN'T)
      if(clockExists($PDO, $_POST['name']))
        die('Clock ' . $_POST['name'] . ' already exists, change it and try again');

      //AND CODE
      if(clockCodeExists($PDO, $_POST['shortName']))
        die('Clock Code ' . $_POST['shortName']
            . ' already exists, change it and try again');

      //CREATE NEW TABLE (CLOCK + RULES)
      createClockTables($PDO, $_POST['name']);

      //SAVE EVENTS TO NEW TABLE
      saveEvents($PDO, $_POST['name'], $_POST['events']);

      //ADD CLOCK TO CLOCKS TABLE: TODO Artist Sep + Remarks
      addClock($PDO, $_POST['name'], $_POST['shortName'], $_POST['colour'], 5, '');

      //ADD CLOCK TO CLOCKS PERMS under currently selected Service
      addClockPerms($PDO, $_POST['name'], $_POST['service']);

      echo 'Clock ' . $_POST['name'] . ' has been saved';

    }else if($_POST['mode'] == 'save'){

      if($_POST['originalName'] != $_POST['name']){

        //CHECK NAME EXISTS (IT SHOULDN'T)
        if(clockExists($PDO, $_POST['name']))
          die('Clock ' . $_POST['name'] . ' already exists, change it and try again');

        //CHECK NEW CODE EXISTS (IT SHOULDN'T)
        if($_POST['shortName'] != $_POST['originalShortName']){

          //Code is changing too, lets check for new code
          if(clockCodeExists($PDO, $_POST['shortName']))
            die('Clock Code ' . $_POST['shortName']
                . ' already exists, change it and try again');

        }

        //CHECK ORIGINAL EXISTS (IT SHOULD)
        if(!clockExists($PDO, $_POST['originalName']))
          die('Original clock ' . $_POST['originalName']
               . ' can\'t be found, cannot rename');

        //SAVE EVENTS TO ORIGINAL CLOCK TABLE
        saveEvents($PDO, $_POST['originalName'], $_POST['events']);

        //UPDATE CODE IF CHANGED
        if($_POST['originalShortName'] != $_POST['shortName'])
          updateClockCode($PDO, $_POST['originalName'], $_POST['shortName']);

        //RENAME TABLES (CLOCK AND CLOCK RULES)
        renameClockTables($PDO, $_POST['originalName'], $_POST['name']);

        //RENAME IN CLOCKS
        renameClock($PDO, $_POST['originalName'], $_POST['name']);

        //RENAME IN CLOCK_PERMS
        renameClockPerms($PDO, $_POST['originalName'], $_POST['name']);

        //RENAME IN GRIDS
        renameClockInGrids($PDO, $_POST['originalName'], $_POST['name']);

        echo 'Clock ' . $_POST['originalName'] . ' has been renamed to ' . $_POST['name'];

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
