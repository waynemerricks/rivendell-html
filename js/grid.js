/**
 * Called when web page finished loading
 */
function onLoaded(){

  /*** Clock Palette ***/

  //Get all Rivendell Clock elements and add drag listeners
  var clocks = document.getElementsByClassName('rivclock');

  for(i = 0; i < clocks.length; i++){

    clocks[i].addEventListener('dragstart', dragStart, false);

  }

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

}

/**
 * Called when any of our listeners start dragging
 */
function dragStart(e){

  console.log('Dragging: ' + e.target.getAttribute('id'));
  e.dataTransfer.effectAllowed='move';
  e.dataTransfer.setData('Text', e.target.getAttribute('id'));
  e.dataTransfer.setDragImage(e.target, 0, 0);

  //Log what clock is being dragged
  currentRivClock = e.target.getAttribute('id');

}

/**
 * Called when anything is dragged into this box
 */
function dragEnter(e){

  e.preventDefault();
  console.log('Entered: ' + e.target.getAttribute('id'));

  //Amend border to show selection (if it doesn't have it already)
  var classes = e.target.className;

  if(classes.indexOf('selected') == -1)
    e.target.className += ' selected';

}

/**
 * Called when anything that is dragged, leaves this box
 */
function dragLeave(e){

  e.preventDefault();
  console.log('Left: ' + e.target.getAttribute('id'));

}

/**
 * Called when clocks are dropped
 */
function dropped(e){

  e.preventDefault();
  console.log('Dropped: ' + e.target.getAttribute('id'));

  //reset selected
  var selectedGrids = document.getElementsByClassName('selected');

  /* When you remove 'selected' from a grid, the element is also
   * removed from the selectedGrids variable automatically.
   * Hence you need a loop to just loop while length > 0
   *
   * Took a while to figure that stupidity out.
   */
  while(selectedGrids.length > 0){

    selectedGrids[0].className = 'clock';

  }

}

//Hook into web page load
window.addEventListener("load", onLoaded, false);


