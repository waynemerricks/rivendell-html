/**
 * "Class" that holds Clock data and associated event names
 */
var Clock = function(name, shortName){

  this.name = name;
  this.shortName = shortName;
  this.events = [];

}

Clock.prototype.addStart(event){

  events.unshift(event);

}

Clock.prototype.addEnd(event){

  events.push(event);

}

Clock.prototype.addEvent(event, index){

  events.splice(index, 0, event);

}

Clock.prototype.removeEvent(index){

  events.splice(index, 1);

}
//End of Clock "Class"
