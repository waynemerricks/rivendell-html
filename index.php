<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Rivendell Grid</title>
        <script src="js/grid.js" type="text/javascript"></script>
        <link href="css/grid.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <div id="clocks">
          <div style="background: red" id="HR1" class="rivclock" draggable="true">HR1</div>
          <div style="background: blue" id="HR2" class="rivclock" draggable="true">HR2</div>
          <div class="spacer"></div>
        </div>
        <div id="grid">
<?php

    $days=array('mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun');
    foreach($days as $day){

?>
          <div id="<?php echo $day; ?>">
              <h2><?php echo $day; ?></h2>
<?php
        for($hour = 0; $hour < 24; $hour++){
?>
              <div class="clock" id="<?php echo $day . '-' . sprintf('%02d', $hour); ?>"><?php echo sprintf('%02d', $hour) . '-' . sprintf('%02d', $hour + 1); ?></div>
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
