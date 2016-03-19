<?php

  //Include files
  include('../../config/database.php');
  include('includes/database.php');

  //Open database connection
  $PDO = getDatabaseConnection();

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Rivendell Grid</title>
        <script src="js/grid.js" type="text/javascript"></script>
        <link href="css/grid.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <div id="clocks">
          <h2>Clocks</h2>
<?php

  $clocks = getRivendellClocks($PDO, 'Production');

  foreach($clocks as $clock){

?>
          <div style="background: <?php echo $clock['color']; ?>"
               id="<?php echo $clock['short_name']; ?>" class="rivclock"
               draggable="true"><?php echo $clock['short_name']; ?></div>
          <div class="clockName"><?php echo $clock['name']; ?></div>
<?php
  }
?>
          <div class="spacer"></div>
        </div>
        <div id="grid">
          <h2 class="left">Grid</h2>
<?php

    $clockNo = -1;
    $days=array('mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun');

    foreach($days as $day){

?>
          <div id="<?php echo $day; ?>">
              <h2><?php echo $day; ?></h2>
<?php

        for($hour = 0; $hour < 24; $hour++){

          $clockNo++;

?>
              <div class="clock" id="clock<?php echo $clockNo; ?>">
                <div>
<?php echo sprintf('%02d', $hour) . '-' . sprintf('%02d', $hour + 1); ?>
                </div>
                <div class="data" id="clock<?php echo $clockNo; ?>Data"></div>
                <div name="clock<?php echo $clockNo; ?>Close" class="close" onClick="clearGrid('clock<?php echo $clockNo; ?>');"></div>
              </div>
<?php
        }//End Hour For Loop
?>
            <div class="spacer"></div>
          </div>
<?php
    }//End days loop

?>
      </div>

    </body>
</html>
<?php

  //Close DB
  $PDO = NULL;

?>
