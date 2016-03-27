/**
 * Called when web page finished loading
 */
function onLoaded(){

  /*** Event Palette ***/
  console.log('Loaded');

  //Get all Rivendell Event elements and add drag listeners
  var events = document.getElementsByClassName('event');

  for(i = 0; i < events.length; i++)
    addDraggableListeners(events[i]);

  /*** Drop Targets ***/
  addDragAndDropListeners(document.getElementById('start'));
  var targets = document.getElementById('editor')
        .getElementsByClassName('event');

  for(i = 0; i < targets.length; i++)
    addDragAndDropListeners(targets[i]);

  var timeElements = document.getElementById('editor')
        .getElementsByClassName('eventTime');

  for(i = 0; i < timeElements.length; i++)
    addMouseClickListener(timeElements[i]);

  //Add to post divs too
  var targets = document.getElementById('editor')
        .getElementsByClassName('post');

  for(i = 0; i < targets.length; i++)
    addDragAndDropListeners(targets[i]);

  //Need to prevent Events being dropped onto the event pallette / themselves
  var eventPallete = document.getElementById('events')
        .getElementsByClassName('event');

  for(i = 0; i < eventPallete.length; i++)
    preventDefaultDnD(eventPallete[i]);

  //Add mouse listener to pallete eventTime labels
  var eventPalleteTimes = document.getElementById('events')
        .getElementsByClassName('eventTime');

  for(i = 0; i < eventPalleteTimes.length; i++)
    addMouseClickListener(eventPalleteTimes[i]);

  //Need to track what targets we've entered and what clock is being dragged
  currentEvent = '';
  validDrop = false;//Flag to test for dnd cancel
  currentEvents = 0; //counter for events in this clock
  eventNumber = 0;

  calculateTimeLeft();

}

/**
 * Called when any of our listeners start dragging
 */
function dragStart(e){

  console.log('Dragging: ' + e.target.getAttribute('id'));
  e.dataTransfer.effectAllowed='copy';
  e.dataTransfer.setData('Text', e.target.getAttribute('id'));
  e.dataTransfer.setDragImage(e.target, 0, 0);

  //Log what event is being dragged
  currentEvent = e.target.getAttribute('id');

}

/**
 * Called when drag event stops
 */
function dragStopped(e){

  e.preventDefault();
  var droppedId = e.target.getAttribute('id');
  console.log('Stopped: ' + droppedId);

  //Populate targets with dragged item
  var selectedGrids = document.getElementsByClassName('selected');

  //TODO Loop selected event grids
  while(selectedGrids.length > 0){

    var classes = selectedGrids[0].className;
    var originalClass = '';

    if(classes.indexOf('bookends') != -1)
      originalClass = 'bookends';
    else if(classes.indexOf('post') != -1)
      originalClass = 'post';
    else if(classes.indexOf('event') != -1)
      originalClass = 'event';

    selectedGrids[0].setAttribute('class', originalClass);

  }

  //Reset current dragged just in case
  currentEvent = '';
  validDrop = false; //Reset valid drop var

  calculateTimeLeft();

}

/**
 * Called when anything is dragged into this box
 */
function dragEnter(e){

  e.preventDefault();

  if(e.target instanceof HTMLDivElement){

    var id = e.target.getAttribute('id');

    console.log('Entered: ' + id);

    if(id != null){//null so you don't pick up text elements

      //Amend border to show selection (if it doesn't have it already)
      var classes = e.target.className;

      if(classes.indexOf('selected') == -1){

        if(classes.indexOf('bookends') != -1
              || classes.indexOf('event') != -1
              || classes.indexOf('post') != -1)
          e.target.className += ' selected';


      }

    }

  }

}

/**
 * Called when anything that is dragged, leaves this box
 */
function dragLeave(e){

  e.preventDefault();

  if(e.target instanceof HTMLDivElement){

    console.log('Left: ' + e.target.getAttribute('id'));

      if(e.target.getAttribute('id') != null){

      if(e.target.className.indexOf('bookends') != -1)
        e.target.className = 'bookends';

      if(e.target.className.indexOf('pre') != -1)
        e.target.className = 'pre';

      if(e.target.className.indexOf('post') != -1)
        e.target.className = 'post';

      if(e.target.className.indexOf('event') != -1)
        e.target.className = 'event';

    }

  }

}

/**
 * Helper function to test for all known bad drop target
 * conditions:
 * - Dropping onto self
 * - Dropping onto my own post div
 * - Dropping onto null (sometimes have weirdness)
 * @param currentDraggedId The id of the div we're dragging
 * @param dropTarget HTMLElement representing the drop target
 * @return true if we can drop here
 */
function canDropHere(currentDraggedId, dropTarget){

  var canDrop = true;
  var targetId = dropTarget.getAttribute('id');

  if(currentDraggedId == targetId)
    canDrop = false;

  if(targetId == null)
    canDrop = false;

  if(dropTarget.getAttribute('id') == 'post'){

    var sib = dropTarget.previousElementSibling;

    if(sib.getAttribute('id') == currentDraggedId)
      canDrop = false;

  }

  if(currentDraggedId == 'Delete Event'
        && dropTarget.getAttribute('id') == 'start')
    canDrop = false;

  console.log('Can Drop: ' + canDrop);
  return canDrop;

}


/**
 * Called when events are dropped
 */
function dropped(e){

  e.preventDefault();

  var targetId = e.target.getAttribute('id');
  console.log('Dropped: ' + currentEvent + ' onto ' + targetId);

  //Make sure you don't drag onto self
  if(canDropHere(currentEvent, e.target)){

    if(!isDelete(targetId)){

      var eventGrid = document.getElementById('editor');
      var clonedEvent = createEventDiv(currentEvent);
      var spacers = true;

      if(e.target.getAttribute('id') == 'start'){

        //Insert After Start
        var afterThis = document.getElementById('start');
        eventGrid.insertBefore(clonedEvent, afterThis.nextElementSibling);

      }else if(e.target.getAttribute('id') == 'post'){

        //We want to dop after the parent element of this
        var parentEvent = e.target.getAttribute('parent');
        parentEvent = document.getElementById(parentEvent);

        eventGrid.insertBefore(clonedEvent, parentEvent.nextElementSibling
            .nextElementSibling);//2nextSibs to skip over post target

      }else{

        //Replace existing element
        console.log('Replace element');
        //Change post div to new parent id
        e.target.parentNode.replaceChild(clonedEvent, e.target);
        clonedEvent.nextElementSibling.parentNode.removeChild(
            clonedEvent.nextElementSibling);

      }

      //Create spacers for insertion/moving divs
      if(spacers)
        createSpacers(clonedEvent);

      addDraggableListeners(clonedEvent);
      addDragAndDropListeners(clonedEvent);

      validDrop = true;

    }else{//End !isDelete

      //Delete this element and its post div
      var deleteMe = e.target;
      var postDiv = deleteMe.nextElementSibling;
      deleteMe.parentNode.removeChild(deleteMe);
      postDiv.parentNode.removeChild(postDiv);

    }

  }//End Can Drop Here

}

/**
 * Checks if the dragged event is the delete clock event
 * @return true if this is a delete event
 */
function isDelete(){

  var deleteEvent = false;

  if(currentEvent == 'Delete Event')
    deleteEvent = true;

  return deleteEvent;

}

/**
 * Creates an event div based on the existing event palette
 * @param eventName Name of Event Div to base this off
 * @return HTMLElement containing event and DND stuff
 */
function createEventDiv(eventName){


  /* Check we don't already have a !JS! tag
   * If we do then don't clone this event just use it
   */
  var event = document.getElementById(eventName);
  var id = event.getAttribute('id');

  if(id.indexOf('!JS!') != 0){

    console.log('Cloning: ' + eventName);
    event = event.cloneNode(true);
    id = '!JS!_' + eventNumber + '_'  + id;
    event.setAttribute('id', id);
    eventNumber++;

    //Add the mouse click listener back to the eventTime element
    var times = event.getElementsByClassName('eventTime');

    for(i = 0; i < times.length; i++)
      addMouseClickListener(times[i]);

  }else{

    //Already have JS so this is a move not clone
    console.log('Moving: ' + eventName);

    //Need to remove trailing post div
    var removePostDiv = event.nextElementSibling;

    console.log('Removing Post: ' + removePostDiv.getAttribute('parent'));
    removePostDiv.parentNode.removeChild(removePostDiv);

  }

  return event;

}

/**
 * Creates necessary spacers for event div to enable drag and drop as well
 * as further insertions or moving oher divs later on
 * @param index Position in list to insert (0 = start, -1 end)
 * @param eventDiv Div that was inserted and needs spacers
 */
function createSpacers(eventDiv){

  console.log('Adding spacers to: ' + eventDiv.getAttribute('id'));

  var eventGrid = document.getElementById('editor');

  var post = document.createElement('div');
  post.setAttribute('id', 'post');
  post.setAttribute('class', 'post');
  post.setAttribute('parent', eventDiv.getAttribute('id'));

  eventGrid.insertBefore(post, eventDiv.nextElementSibling);

  addDragAndDropListeners(post);

}

/**
 * Helper function to enable this element to become draggable
 * @param Element to add dragstart and dragend listeners to
 */
function addDraggableListeners(element){

    element.addEventListener('dragstart', dragStart, false);
    element.addEventListener('dragend', dragStopped, false);

}

/**
 * Add mouse click listener to element
 * @param element to listen for clicks
 */
function addMouseClickListener(element){

  element.addEventListener('click', durationClicked, false);

}

/**
 * Helper function to add DnD listeners to a given element
 * Adds dragover, dragenter, dragleave and drop
 * @param element Element to add listeners to
 */
function addDragAndDropListeners(element){

  element.addEventListener('dragover',
      function(e){e.preventDefault();}, false);
  element.addEventListener('dragenter', dragEnter, false);
  element.addEventListener('dragleave', dragLeave, false);
  element.addEventListener('drop', dropped, false);

}

/**
 * Prevent dnd target events on given element
 * @param element Adds a preventDefault listener to this element
 */
function preventDefaultDnD(element){

  element.addEventListener('dragover',
      function(e){e.preventDefault();}, false);
  element.addEventListener('dragenter',
      function(e){e.preventDefault();}, false);
  element.addEventListener('dragleave',
      function(e){e.preventDefault();}, false);
  element.addEventListener('drop',
      function(e){e.preventDefault();}, false);

}

/**
 * Called when delete clock is clicked
 */
function deleteClock(){

  var clock = document.getElementsByName('originalName');

  if(clock[0].value == 'Add New Clock')
    alert('Can\'t delete this clock');//You were trying to add one
  else if(confirm('Delete current clock from database?')){

    var deleteMe = jQuery.post('deleteClock.php', { name: clock[0].value })
        .done(function(data){

          alert(data);

        })
        .fail(function(XMLHttpRequest, textStatus, errorThrown){

          alert('Failed to delete Clock(' + XMLHttpRequest.status + ') '
              + XMLHttpRequest.statusText);
          console.log(XMLHttpRequest);

        });

  }

}

/**
 * Called when clear clock is clicked
 */
function emptyClock(){

  if(confirm('Clear all events from current clock?')){

    var editor = document.getElementById('editor');

    var temp = editor.getElementsByClassName('post');

    while(temp.length > 0)
      editor.removeChild(temp[0]);

    temp = editor.getElementsByClassName('event');

    while(temp.length > 0)
      editor.removeChild(temp[0]);

    calculateTimeLeft();

  }

}

/**
 * Called when Save as is clicked
 * Will save the current clock under a new name given by the clockName and code
 * currently in the editor form
 */
function saveAsClock(){

  if(confirm('Save this clock under new name?')){

    save('saveas');

  }

}

/**
 * Called by as part of saveClock or saveAsClock
 * @param mode: save, saveas
 */
function save(mode){

    if(document.getElementById('clockTimeLeft').getAttribute('class')
          .indexOf('overLimit') != -1){

      //Clock is too full alert user
      alert('Clock is over filled, please reduce content to 60minutes or less');

    }else{//Clock not over filled

      //We can try to save this
      //Get array of events [EVENT_NAME, START_TIME, LENGTH]
      var saveEvents = document.getElementById('editor')
            .getElementsByClassName('event');

      var timeMillis = 0;
      var saveMe = [];

      for(i = 0; i < saveEvents.length; i++){

        var event = [];
        event.push(saveEvents[i].children[0].innerHTML);//EVENT_NAME
        event.push(timeMillis);//START_TIME
        var eventMillis = getMillisFromTime(saveEvents[i].children[2]
              .innerHTML);
        event.push(eventMillis);//LENGTH
        timeMillis += eventMillis; //next event START_TIME

        saveMe.push(event);

      }//End for saveEvents

      var name = document.getElementById('clockName').value;
      var originalName = document.getElementById('originalName').value;
      var shortName = document.getElementById('clockShortName').value;
      var originalShortName = document.getElementById('originalShortName')
            .value;
      var colour = document.getElementById('clockColour').value;

      //Post it
      var save = jQuery.post('saveClock.php', { name: name,
            shortName: shortName,
            originalName: originalName,
            originalShortName: originalShortName,
            colour: colour,
            mode: mode,
            events: saveMe })
        .done(function(data){

          alert(data);

        })
        .fail(function(XMLHttpRequest, textStatus, errorThrown){

          alert('Failed to save Clock (' + XMLHttpRequest.status + ') '
              + XMLHttpRequest.statusText);
          console.log(XMLHttpRequest);

        });

    }//End if clock over filled

}

/**
 * Called when save clock is clicked
 */
function saveClock(){

  if(confirm('Save this clock to database?')){

    save('save');

  }//End If Confirm save

}

/**
 * Checks for timed events and alters the property to show the time
 * @param element eventTime element
 * @param millis current millis at this point in the clock
 */
function displayHardTime(element, millis){

  var propertyElement = element.previousElementSibling;

  if(propertyElement.innerHTML.indexOf('Timed') != -1){

    if(propertyElement.innerHTML.length > 10
        && propertyElement.innerHTML.indexOf('[T') == 0){

      /* Already have a timed event, need to remove this
       * before recalculating. */
      propertyElement.innerHTML = propertyElement.innerHTML.substring(12);

    }

    //This is a timed event
    propertyElement.innerHTML = '[T ' + getTimeFromMillis(millis) + '] '
        + propertyElement.innerHTML;
    propertyElement.style.color = 'blue';
    propertyElement.style.fontWeight = 'bold';

  }

}

/**
 * Converts the given millis to MM:SS.s
 * @param millis milliseconds to convert
 * @return millis converted into MM:SS.s
 */
function getTimeFromMillis(millis){

  var minutes = '' + Math.floor(millis/60000);
  millis = millis % 60000;

  while(minutes.length < 2)
    minutes = '0' + minutes;

  var seconds = '' + Math.floor(millis/1000);
  if(seconds < 0)
    seconds = seconds * -1;

  while(seconds.length < 2)
    seconds = '0' + seconds;

  millis = millis % 1000;

  var tenths = Math.floor(millis/100);

  if(tenths < 0)
    tenths = tenths * -1;

  return minutes + ':' + seconds + '.' + tenths;

}

/**
 * Parses time MM:SS.s into millis
 * @param time time to parse
 * @return millis
 */
function getMillisFromTime(time){

    time = time.split(':');
    var millis = 0;

    if(time.length == 2 && time[1].indexOf('.') != -1){

      var temp = time[1].split('.');

      time[1] = temp[0];
      time.push(temp[1]);

    }

    if(time.length >= 2){

      millis += time[0] * 60000;
      millis += time[1] * 1000;

    }

    if(time.length == 3)
      millis += time[2] * 100;

    return millis;

}

/**
 * Called to calculate how much time is left in this clock
 */
function calculateTimeLeft(){

  var editor = document.getElementById('editor');
  var times = editor.getElementsByClassName('eventTime');
  var millis = 0;

  for(i = 0; i < times.length; i++){

    displayHardTime(times[i], millis);//Display [T HH:MM] if timed event
    millis += getMillisFromTime(times[i].innerHTML);

  }//End time For

  millis = 3600000 - millis;

  var tlClass = 'normal';//white

  if(millis < 300000)
    tlClass = 'nearlyFull';//yellow

  if(millis == 0)
    tlClass = 'full';//green

  if(millis < 0)
    tlClass = 'overLimit';//red

  var timeLeft = document.getElementById('clockTimeLeft');
  timeLeft.value = getTimeFromMillis(millis);
  timeLeft.setAttribute('class', tlClass);

  console.log(timeLeft.value);

}

/**
 * Called by mouse click listener on event times
 */
function durationClicked(e){

  var duration = e.target.innerHTML;
  var events = document.getElementById('events');
  var name = e.target.parentElement.getAttribute('id');

  if(name.indexOf('!JS!') == 0){

    name = name.substring(5);//remove !JS!_
    name = name.substring(name.indexOf('_') + 1);

  }

  duration = changeDuration(name, duration);

  e.target.innerHTML = duration;

  calculateTimeLeft();

}

/**
 * Called when time is clicked on event
 * @param name to display for event
 * @duration current duration of event
 */
function changeDuration(name, duration){

  var newDuration = prompt('Change duration for ' + name + '(MM:SS.s)',
        duration);

  if(newDuration != null){

    //Validate new duration
    var temp = newDuration.split(':');

    if(temp.length == 2 && checkTimeRange(temp[0])){ //MM OK

      if(temp[1].indexOf('.') == -1){//No tenths

        if(checkTimeRange(temp[1]))
          temp = true;

      }else{//Yes tenths

        var secTemp = temp[1].split('.');

        if(secTemp.length == 2){

          if(checkTimeRange(secTemp[0]) && secTemp[1] >= 0 && secTemp[1] <= 9)
            temp = true;

        }

      }

    }

  }

  if(temp == null || temp != true)
    newDuration = duration;//Revert to old value

  return newDuration;

}

//Simple 0 to 59 check
function checkTimeRange(value){

  var valid = false;

  if(value >= 0 && value <= 59)
    valid = true;

  return valid;

}

//Hook into web page load
window.addEventListener('load', onLoaded, false);
