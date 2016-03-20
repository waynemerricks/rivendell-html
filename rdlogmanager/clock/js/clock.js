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
 * Called when mouse is over any of the grid targets
 */
function mouseEnter(e){

  var clockId = e.target.getAttribute('id');

  //Check if we have data, if yes, show close icon
  if(document.getElementById(clockId + 'Data').innerHTML.length > 0){

    console.log('Showing Close for: ' + e.target.getAttribute('id'));
    var closeDiv = document.getElementsByName(clockId + 'Close');
    closeDiv[0].style.display = 'block';

  }

}

function mouseLeave(e){

  var clockId = e.target.getAttribute('id');

  //Check if we have data, if yes, hide close icon
  if(document.getElementById(clockId + 'Data').innerHTML.length > 0){

    console.log('Hiding Close for: ' + e.target.getAttribute('id'));
    var closeDiv = document.getElementsByName(clockId + 'Close');
    closeDiv[0].style.display = 'none';

  }

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

  //BUG FIX: Can be TEXT if you drag directly on text instead of DIV
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
  var newEvent = document.createElement('div');
  newEvent.innerHTML='New Thing here';
  newEvent.setAttribute('class', 'event');

  if(e.target.getAttribute('id') == 'start'){

    //Insert After Start
    var afterThis = document.getElementById('start');
    eventGrid.insertBefore(newEvent, afterThis.nextSibling);

  }else if(e.target.getAttribute('id') == 'end'){

    //Insert Before End
    var beforeThis = document.getElementById('end');
    eventGrid.insertBefore(newEvent, beforeThis);

  }

  validDrop = true;

}

/**
 * Called when clear grids are clicked
 */
function clearGrid(gridId){

  console.log('Clearing: ' + gridId);

  //Set Grid back to white
  var grid = document.getElementById(gridId);
  grid.style.background = 'white';

  //Clear Data Div and hide
  var clearDataDiv = document.getElementById(gridId + 'Data');
  clearDataDiv.innerHTML = '';
  clearDataDiv.style.display = 'none';

  //Make sure close is hidden
  var closeDiv = document.getElementsByName(gridId + 'Close');
  closeDiv[0].style.display = 'none';

}

/**
 * Clears all grid entries
 */
function emptyGrid(){

  if(confirm('Clear current grid of all clocks?')){

    for(i = 0; i < 168; i++)
      clearGrid('clock' + i);

  }

}

/**
 * Saves the current grid in the services table
 */
function saveGrid(serviceName){

  if(confirm('Save current grid for ' + serviceName + '?')){

    //Assemble grid clocks
    var gridClocks = [168];

    for(i = 0; i < 168; i++){

      var clockId = document.getElementById('clock' + i + 'Data').innerHTML;
      gridClocks[i] = document.getElementById(clockId + '_name').innerHTML;

    }

    /* Now we have a 168 element array with the clock names
     * use JQuery to post data */

    var save = jQuery.post('saveGrid.php', { service: serviceName,
          grid: gridClocks })
        .done(function(data){

          alert(data);

        })
        .fail(function(XMLHttpRequest, textStatus, errorThrown){

          alert('Failed to save Grid (' + XMLHttpRequest.status + ') '
              + XMLHttpRequest.statusText);
          console.log(XMLHttpRequest);

        });

  }

}

//Hook into web page load
window.addEventListener('load', onLoaded, false);
