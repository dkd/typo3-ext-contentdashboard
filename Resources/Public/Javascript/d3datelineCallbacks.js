/**
 * loadchart from d3dateline.js expected callback functions:
 * itemclick, itemmouseover and itemmouseout
 */


function itemclick( d ) {
    // this function will be called when a node is clicked
}

function itemmouseover( d ) {
    d3.select('#tooltip' + d.id).classed('open', true);
}

function itemmouseout( d ) {
    d3.select('#tooltip' + d.id).classed('open', false);
}
