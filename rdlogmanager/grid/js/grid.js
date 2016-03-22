/**
 * Called when web page finished loading
 */
function onLoaded(){

  /*** Clock Palette ***/

  //Get all Rivendell Clock elements and add drag listeners
  var clocks = document.getElementsByClassName('rivclock');

  for(i = 0; i < clocks.length; i++){

    clocks[i].addEventListener('dragstart', dragStart, false);
    clocks[i].addEventListener('dragend', dragStopped, false);

  }

  //Add drag events to clock deleter element
  var deleteClock = document.getElementById('deleteClock');
  deleteClock.addEventListener('dragstart', dragStart, false);
  deleteClock.addEventListener('dragend', dragStopped, false);

  /*** Grid Targets ***/

  //Get all grids and add drag listeners
  var grids = document.getElementsByClassName('clock');

  for(i = 0; i < grids.length; i++){

    grids[i].addEventListener('dragover', function(e){e.preventDefault();}, false);
    grids[i].addEventListener('dragenter', dragEnter, false);
    grids[i].addEventListener('dragleave', dragLeave, false);
    grids[i].addEventListener('drop', dropped, false);

  }

  //Need to track what targets we've entered and what clock is being dragged
  enteredTargets = [];
  currentRivClock = '';
  validDrop = false;//Flag to test for dnd cancel

}

/**
 * Called when any of our listeners start dragging
 */
function dragStart(e){

  console.log('Dragging: ' + e.target.getAttribute('id'));
  e.dataTransfer.effectAllowed='copy';
  e.dataTransfer.setData('Text', e.target.getAttribute('id'));
  e.dataTransfer.setDragImage(e.target, 0, 0);

  //Log what clock is being dragged
  currentRivClock = e.target.getAttribute('id');

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

  /* When you remove 'selected' from a grid, the element is also
   * removed from the selectedGrids variable automatically.
   * Hence you need a loop to just loop while length > 0
   *
   * Took a while to figure that stupidity out.
   */

  while(selectedGrids.length > 0){

    if(validDrop){

      /* Change data div to dragged clock unless its a delete
       * in which case just clear it
       */
      var dataDiv = document.getElementById(
            selectedGrids[0].getAttribute('id') + 'Data');

      if(droppedId == 'deleteClock'){

        clearGrid(selectedGrids[0].getAttribute('id'));

      }else{//Not delete so show divs and change colour to clock

        dataDiv.innerHTML = e.target.getAttribute('id');
        dataDiv.style.display = 'block';
        selectedGrids[0].style = e.target.getAttribute('style');
        selectedGrids[0].setAttribute('name', e.target.getAttribute('id'));

      }

    }

    selectedGrids[0].className = 'clock';

  }

  //Reset current dragged just in case
  currentRivClock = '';
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

    if(classes.indexOf('selected') == -1 && classes.indexOf('clock') != -1)
      e.target.className += ' selected';

  }

}

/**
 * Called when anything that is dragged, leaves this box
 */
function dragLeave(e){

  e.preventDefault();

  if(e.target instanceof HTMLDivElement
      && e.target.className.indexOf('clock') != -1){

    console.log('Left: ' + e.target.getAttribute('id'));

    //If we aren't holding control, unselect when we leave the box
    if(!e.ctrlKey)
      e.target.className = 'clock';

  }

}

/**
 * Called when clocks are dropped
 */
function dropped(e){

  e.preventDefault();

  console.log('Dropped: ' + currentRivClock + ' onto ' +
      e.target.getAttribute('id'));

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
