<?php

  if(isset($_POST['name'], $_POST['shortName'], $_POST['originalName'],
        $_POST[originalShortName'], $_POST['mode'], $_POST['events'])){

    var_dump($_POST);

    if($_POST['mode'] == 'save'){

      if($_POST['originalName'] != $_POST['name']){
        echo 'RENAME';
        //CHECK NAME EXISTS (IT SHOULDN'T)
        //CHECK ORIGINAL EXISTS (IT SHOULD)
        //SAVE EVENTS TO ORIGINAL CLOCK TABLE
        //RENAME TABLES (CLOCK AND CLOCK RULES)
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

  }else
    echo 'Incorrect usage';

?>
