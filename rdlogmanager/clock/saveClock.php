<?php

  include('../../config/database.php');
  include('includes/commonFunctions.php');
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
      //Make sure we have a code, if not try first 3 chars of name
      if(strlen($_POST['shortName']) < 1)
        $_POST['shortName'] = substr($_POST['name'], 0, 3);
      
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
        if(!clockExists($PDO, $_POST['originalName']))
          die('Can\'t save as existing clock is missing from database: '
              . $_POST['originalName']);

        //NAME CHANGES ARE HANDLED ABOVE
        //CHECK SHORT NAME is SAME
        if($_POST['originalShortName'] != $_POST['shortName']){

          //Need to update short name
          //MAKE SURE ORIGINAL CODE EXISTS
          if(!clockCodeExists($_POST['originalShortName']))
            die('Existing clock code is missing from database: '
                . $_POST['originalShortName']);

          //MAKE SURE NEW CODE DOESN'T EXIST
          if(clockCodeExists($_POST['shortName']))
            die('Can\'t save new clock code as it already exists');

          //Checks complete, update code
          //AMEND SHORT NAME
          updateClockCode($PDO, $_POST['originalName'], $_POST['shortName']);

        }

        //SAVE EVENTS TO CLOCK TABLE
        saveEvents($PDO, $_POST['originalName'], $_POST['events']);

      }

    }else if($_POST['mode'] == 'saveas'){

      if($_POST['originalName'] != $_POST['name']){

        //CHECK NAME EXISTS (IT SHOULDN'T)
        if(clockCodeExists($_POST['name']))
          die('Can\'t save as this name, it already exists: ' . $_POST['name']);

        //CHECK ORIGINAL EXISTS (IT SHOULD)
        if(!clockCodeExists($_POST['originalName']))
          die('Can\'t save this clock, the original clock is missing from the database');

        //COPY ORIGINAL TABLE
        copyClockTable($PDO, $_POST['originalName'], $_POST['name']);

        //COPY ORIGINAL RULES
        copyClockRulesTable($PDO, $_POST['originalName'], $_POST['name']);

        //SAVE EVENTS TO NEW TABLE
        /* We save the events separately because the user might have tried to copy this
         * clock with amended events */
        saveEvents($PDO, $_POST['name'], $_POST['events']);

      }else{
        echo 'Can\'t save as same clock';
      }

    }

    $PDO = NULL;

  }else
    echo 'Incorrect usage';

?>
