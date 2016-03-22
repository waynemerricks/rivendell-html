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
  var targets = [ document.getElementById('start'),
                  document.getElementById('end') ];

  for(i = 0; i < targets.length; i++)
    addDragAndDropListeners(targets[i]);

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
  console.log(e);

  if(e.target instanceof HTMLDivElement){

    console.log('Entered: ' + e.target.getAttribute('id'));

    //Amend border to show selection (if it doesn't have it already)
    var classes = e.target.className;

    if(classes.indexOf('selected') == -1
          && (classes.indexOf('bookends') != -1
          || classes.indexOf('event')) )
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
  var insertIndex = 0; //Default to start of list

  if(e.target.getAttribute('id') == 'start'){

    //Insert After Start
    var afterThis = document.getElementById('start');
    eventGrid.insertBefore(clonedEvent, afterThis.nextSibling);

  }else if(e.target.getAttribute('id') == 'end'){

    //Insert Before End
    var beforeThis = document.getElementById('end');
    eventGrid.insertBefore(clonedEvent, beforeThis);
    insertIndex = -1;

  }else{

    //Insert somewhere else in list
    insertIndex = 1;

  }

  //Create spacers for insertion/moving divs
  createSpacers(insertIndex, clonedEvent);
  addDraggableListeners(clonedEvent);
  addDragAndDropListeners(clonedEvent);

  validDrop = true;

}

/**
 * Creates an event div based on the existing event palette
 * @param eventName Name of Event Div to base this off
 * @return HTMLElement containing event and DND stuff
 */
function createEventDiv(eventName){

  console.log('Cloning: ' + eventName);

  //Clone event
  return document.getElementById(eventName).cloneNode(true);

}

/**
 * Creates necessary spacers for event div to enable drag and drop as well
 * as further insertions or moving oher divs later on
 * @param index Position in list to insert (0 = start, -1 end)
 * @param eventDiv Div that was inserted and needs spacers
 */
function createSpacers(index, eventDiv){

  console.log('Adding spacers to: ' + eventDiv.getAttribute('id')
      + ', index: ' + index);

  var eventGrid = document.getElementById('editor');

  if(index != 0){

    var pre = document.createElement('div');
    pre.setAttribute('class', 'pre');
    pre.setAttribute('id', 'pre');
    pre.setAttribute('parent', eventDiv.getAttribute('id'));

    eventGrid.insertBefore(pre, eventDiv);

    addDragAndDropListeners(pre);

  }

  if(index != -1){

    var post = document.createElement('div');
    post.setAttribute('id', 'pre');
    post.setAttribute('class', 'post');
    post.setAttribute('parent', eventDiv.getAttribute('id'));

    eventGrid.insertBefore(post, eventDiv.nextSibling);

    addDragAndDropListeners(post);

  }

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

//Hook into web page load
window.addEventListener('load', onLoaded, false);
