/**
 * Called when web page finished loading
 */
function onLoaded(){

  /*** Event Palette ***/

  //Get all Rivendell Event elements and add drag listeners
  var events = document.getElementsByClassName('event');

  for(i = 0; i < events.length; i++)
    addDraggableListeners(events[i]);

  /*** Start / Stop Targets ***/
  var targets = [ document.getElementById('start') ];

  for(i = 0; i < targets.length; i++)
    addDragAndDropListeners(targets[i]);

  //Need to prevent Events being dropped onto the event pallette / themselves
  var eventPallete = document.getElementById('events')
        .getElementsByClassName('event');

  for(i = 0; i < eventPallete.length; i++)
    preventDefaultDnD(eventPallete[i]);

  //Need to track what targets we've entered and what clock is being dragged
  currentEvent = '';
  validDrop = false;//Flag to test for dnd cancel
  currentEvents = 0; //counter for events in this clock
  eventNumber = 0;

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
 * Called when events are dropped
 */
function dropped(e){

  e.preventDefault();

  var targetId = e.target.getAttribute('id');
  console.log('Dropped: ' + currentEvent + ' onto ' + targetId);

  //Make sure you don't drag onto self
  if(currentEvent != targetId && targetId != null){

    var eventGrid = document.getElementById('editor');
    var clonedEvent = createEventDiv(currentEvent);
    var spacers = true;

    if(e.target.getAttribute('id') == 'start'){

      //Insert After Start
      var afterThis = document.getElementById('start');
      eventGrid.insertBefore(clonedEvent, afterThis.nextSibling);

    }else if(e.target.getAttribute('id') == 'post'){

      //We want to dop after the parent element of this
      var parentEvent = e.target.getAttribute('parent');
      parentEvent = document.getElementById(parentEvent);

      eventGrid.insertBefore(clonedEvent, parentEvent.nextSibling
          .nextSibling);//2nextSibs to skip over post target

    }else{

      //Replace existing element
      console.log('Replace element');
      //Change post div to new parent id
      e.target.parentNode.replaceChild(clonedEvent, e.target);
      clonedEvent.nextSibling.parentNode.removeChild(clonedEvent.nextSibling);

    }

    //Create spacers for insertion/moving divs
    if(spacers)
      createSpacers(clonedEvent);

    addDraggableListeners(clonedEvent);
    addDragAndDropListeners(clonedEvent);

    validDrop = true;

  }

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

  }else{

    //Already have JS so this is a move not clone
    console.log('Moving: ' + eventName);

    //Need to remove trailing post div
    var removePostDiv = event.nextSibling;

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

  eventGrid.insertBefore(post, eventDiv.nextSibling);

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
 * Called when clear clock is clicked
 */
function emptyClock(){

  //TODO

}

/**
 * Called when save clock is clicked
 */
function saveClock(){

  //TODO

}

/**
 * Called to calculate how much time is left in this clock
 */
function calculateTimeLeft(){

  var editor = document.getElementById('editor');
  var times = editor.getElementsByClassName('eventTime');
  var millis = 0;

  for(i = 0; i < times.length; i++){

    var strTime = times[i].innerHTML;
    strTime = strTime.split(':');

    if(strTime.length == 2 && strTime[1].indexOf('.') != -1){

      var temp = strTime[1].split('.');

      strTime[1] = temp[0];
      strTime.push(temp[1]);

    }

    if(strTime.length == 2){

      millis += strTime[0] * 60000;
      millis += strTime[1] * 1000;

    }

    if(strTime.length == 3)
      millis += strTime[2] * 100;

  }//End time For

  millis = 3600000 - millis;
  console.log('Millis Remaining: ' + millis);

  var color = 'white';

  if(millis < 300000)
    color = 'yellow';

  if(millis == 0)
    color = 'green';

  if(millis < 0)
    color = 'red';

  var minutes = Math.floor(millis/60000);
  millis = millis % 60000;

  var seconds = '' + Math.floor(millis/1000);

  while(seconds.length < 2)
    seconds = '0' + seconds;

  millis = millis % 1000;

  var tenths = Math.floor(millis/100);

  var timeLeft = document.getElementById('clockTimeLeft');
  timeLeft.value = minutes + ':' + seconds + '.' + tenths;
  timeLeft.style.background = color;

  console.log(minutes + ':' + seconds + '.' + tenths);

}

//Hook into web page load
window.addEventListener('load', onLoaded, false);
