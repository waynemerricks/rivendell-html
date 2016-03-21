/**
 * Called when web page finished loading
 */
function onLoaded(){

  /*** Event Palette ***/

  //Get all Rivendell Event elements and add drag listeners
  var events = document.getElementsByClassName('event');

  for(i = 0; i < events.length; i++){

    events[i].addEventListener('dragstart', dragStart, false);
    events[i].addEventListener('dragend', dragStopped, false);

  }

  /*** Start / Stop Targets ***/
  var targets = [ document.getElementById('start'),
                  document.getElementById('end') ];

  for(i = 0; i < targets.length; i++){

    targets[i].addEventListener('dragover',
        function(e){e.preventDefault();}, false);
    targets[i].addEventListener('dragenter', dragEnter, false);
    targets[i].addEventListener('dragleave', dragLeave, false);
    targets[i].addEventListener('drop', dropped, false);

  }

  //Need to track what targets we've entered and what clock is being dragged
  currentEvent = '';
  validDrop = false;//Flag to test for dnd cancel
  currentEvents = 0; //counter for events in this clock

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

    selectedGrids[0].setAttribute('class', 'bookends');

  }

  //Reset current dragged just in case
  currentEvent = '';
  validDrop = false; //Reset valid drop var

}

/**
 * Called when anything is dragged into this box
 */
function dragEnter(e){

  e.preventDefault();

  if(e.target instanceof HTMLDivElement){

    console.log('Entered: ' + e.target.getAttribute('id'));

    //Amend border to show selection (if it doesn't have it already)
    var classes = e.target.className;

    if(classes.indexOf('selected') == -1 && classes.indexOf('bookends') != -1)
      e.target.className += ' selected';

  }

}

/**
 * Called when anything that is dragged, leaves this box
 */
function dragLeave(e){

  e.preventDefault();

  if(e.target instanceof HTMLDivElement
      && e.target.className.indexOf('bookends') != -1){

    console.log('Left: ' + e.target.getAttribute('id'));

    //If we aren't holding control, unselect when we leave the box
    if(!e.ctrlKey)
      e.target.className = 'bookends';

  }

}

/**
 * Called when events are dropped
 */
function dropped(e){

  e.preventDefault();

  console.log('Dropped: ' + currentEvent + ' onto ' +
      e.target.getAttribute('id'));

  var eventGrid = document.getElementById('editor');
  var clonedEvent = createEventDiv(currentEvent);
console.log(clonedEvent);
  if(e.target.getAttribute('id') == 'start'){

    //Insert After Start
    var afterThis = document.getElementById('start');
    eventGrid.insertBefore(clonedEvent, afterThis.nextSibling);

  }else if(e.target.getAttribute('id') == 'end'){

    //Insert Before End
    var beforeThis = document.getElementById('end');
    eventGrid.insertBefore(clonedEvent, beforeThis);

  }

  validDrop = true;

}

/**
 * Creates an event div based on the existing event palette
 * @param eventName Name of Event Div to base this off
 * @return HTMLElement containing event and DND stuff
 */
function createEventDiv(eventName){

  console.log('Cloning: ' + eventName);
  return document.getElementById(eventName).cloneNode(true);

}

//Hook into web page load
window.addEventListener('load', onLoaded, false);
