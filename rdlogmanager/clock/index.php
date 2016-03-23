<?php

  //Include files
  include('../../config/database.php');
  include('../common/database.php');
  include('includes/database.php');

  //Open database connection
  $PDO = getDatabaseConnection();
  $serviceNames = getServiceNames($PDO);

  //Check for post with service change
  $selectedService = 0;

  if(isset($_POST['serviceName']) && $_POST['serviceName'] != 0)
    $selectedService = $_POST['serviceName'];

  $title = 'Rivendell Clocks';
  $js = 'clock.js';
  $css = 'clock.css';

  include('../../template/header.php');//Header HTML

?>
        <div id="services">
          <form id="serviceForm" method="post" action="index.php">
            <label for="serviceList">Services:
              <select name="serviceName">
<?php

  $i = -1;

  foreach($serviceNames as $name){

    $i++;
    $selected='';

    if($selectedService == $i)
      $selected = 'selected ';

?>
                <option <?php echo $selected; ?>value="<?php echo $i; ?>"><?php echo $name; ?></option>
<?php
  } //End foreach services
?>
              </select>
            </label>
            <input type="submit" value="Change Service">
            <div id="gridform">
              <button class="red" type="button" onClick="emptyClock()">Clear</button>
              <button class="green" type="button" onClick="saveClock()">Save</button>
           </div>
          </form>
        </div>
        <div id="clocks">
          <h2>Clocks</h2>

<?php

  $clocks = getRivendellClocks($PDO, $serviceNames[$selectedService]);

  //Check GET for clock references
  $selectedClock = '';

  if(isset($_GET['name']))
    $selectedClock = $clocks[$_GET['name']]['NAME'];

  foreach($clocks as $clock){

?>
          <a href="./?name=<?php echo $clock['NAME']; ?>">
          <div style="background: <?php echo $clock['COLOR']; ?>"
               id="<?php echo $clock['SHORT_NAME']; ?>" class="rivclock"
               ><?php echo $clock['SHORT_NAME']; ?></div>
          <div id="<?php echo $clock['SHORT_NAME']; ?>_name" class="clockName"><?php echo $clock['NAME']; ?></div>
          </a>
<?php
  }

?>
          <div class="spacer"></div>
        </div>
        <div id="events">
          <h2>Events</h2>
<?php

  $events = getRivendellEvents($PDO, $serviceNames[$selectedService]);

  foreach($events as $event){
?>
          <div draggable="true" id="<?php echo $event['NAME']; ?>" class="event" style="background: <?php echo $event['COLOR']; ?>">
            <div class="eventName"><?php echo $event['NAME']; ?></div>
            <div class="eventProperties"><?php echo $event['PROPERTIES']; ?></div>
            <div class="eventTime">4:00</div>
          </div>
<?php
  } //End events for each
?>

        </div>
        <div id="editor">
          <h2 class="left">
<?php
    if(strlen($selectedClock) < 1)
      echo '&larr; Select Clock';
    else
      echo 'Editing ' . $selectedClock;
?>
          </h2>
          <input name="originalName" type="hidden" value="<?php echo $selectedClock; ?>">
          <input name="originalShortName" type="hidden" value="<?php echo $clocks[$selectedClock]['SHORT_NAME']; ?>">
          <label for="clockName">Clock Name:</label>
          <input name="clockName" type="text" maxlength="58" value="<?php echo $selectedClock; ?>">
          <label for="clockShortName">Clock Code:</label>
          <input name="clockShortName" type="text" maxlength="3" value="<?php echo $clocks[$selectedClock]['SHORT_NAME']; ?>">
          <label for="clockTimeLeft">Time Left:</label>
          <input id="clockTimeLeft" name="clockTimeLeft" type="text" maxlength="5" value="60:00">
          <div class="clear"></div>
          <div class="bookends" id="start"><p>Add Events to Start</p></div>
          <div class="bookends" id="end"><p>Add Events to End</p></div>
        </div>
<?php


  //Close DB
  $PDO = NULL;

  include('../../template/footer.php');//Footer HTML

?>
