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
  else if(isset($_GET['serviceName']))
    $selectedService = $_GET['serviceName'];

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

    $url = './?name=' . $clock['NAME'];

    if($selectedService != 0)
      $url .= '&serviceName=' . $selectedService;

?>
          <a href="<?php echo $url; ?>">
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

  $deleteEvent = array();
  $deleteEvent['NAME'] = 'Delete Event';
  $deleteEvent['COLOR'] = 'lightgrey';
  $deleteEvent['PROPERTIES'] = '';

  array_unshift($events, $deleteEvent);

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
<?php if(strlen($selectedClock) > 1){ ?>
          <div class="bookends" id="start"><p>Add Events to Start</p></div>
<?php

    //Loop through this clocks events
    $clockEvents = getClock($PDO, $clocks, $selectedClock);

    $i = 1;

    foreach($clockEvents as $event){

      $divId = '!JS!_-' . $i . '_' . $event['EVENT_NAME'];
      $color = $events[$event['EVENT_NAME']]['COLOR'];
      $properties = $events[$event['EVENT_NAME']]['PROPERTIES'];
      $time = getDuration($event['LENGTH']);
?>
          <div id="<?php echo $divId; ?>" class="event" draggable="true" style="background: <?php echo $color; ?>">
            <div class="eventName"><?php echo $event['EVENT_NAME']; ?></div>
            <div class="eventProperties"><?php echo $properties; ?></div>
            <div class="eventTime"><?php echo $time; ?></div>
          </div>
          <div id="post" class="post" parent="<?php echo $divId; ?>"></div>
<?php

      $i++;

    }

  }//End Selected Clock
 ?>
        </div>
<?php


  //Close DB
  $PDO = NULL;

  include('../../template/footer.php');//Footer HTML

?>
