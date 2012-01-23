/**
 * @fileoverview This is an Ajax client for community chess.
 * It is currently under development. It is done with Google Closure Library.
 * http://closure-library.googlecode.com/svn/docs/namespace_goog_dom.html
 */

goog.provide('chess.client');

goog.require('goog.dom');
goog.require('goog.net.XhrIo');

chess.client.el = 'defghij';

/**
 * Chess Ajax client
 *
 * @constructor
 */
chess.client = function() {
  this.board = 'abc';
  console.log('Constructor');
};

chess.client.prototype.setBoard = function(element) {
    this.board = element;
}

chess.client.prototype.createBoard = function() {
    var divBoard = goog.dom.getElement('chessBoard');
    var table = document.createElement('table');
    var row;
    var td = document.createElement('td');
    var img = document.createElement('img');
    var cell;
    var img;

    for (var i = 0; i < 9; i++) {
        row = document.createElement('tr');
        for (var j = 0; j < 9; j++) {
            cell = document.createElement(i == 0 || j == 0 ? 'th' : 'td');

            if (i == 0) {
                cell.innerText = j;
            } else if (j == 0) {
                cell.innerText = i;
            } else {
                img = document.createElement('img');
                img.src = 'figures/K.png';
                img.alt = 'K';
                cell.appendChild(img);
                if((i+j) % 2 == 0) {
                    cell.className = 'blackField';
                } else {
                    cell.className = 'whiteField';
                }
            }
            row.appendChild(cell);
        }
        goog.dom.insertChildAt(table, row, i);
    }
    goog.dom.insertChildAt(divBoard, table, 0);
    console.log(cell);
}

chess.client.prototype.getData = function(url) {
    goog.net.XhrIo.send(url, function(client) {
        console.log(client);
        this.setBoard(event.target.getResponseText());
        //client.el('a');
        //alert(event.target.getResponseText());
    });
    //return obj.getResponse();
}


// If you don't do that, you get an "Uncaught ReferenceError: moose is not defined"
// http://code.google.com/intl/de-DE/closure/compiler/docs/api-tutorial3.html#export
window['client'] = chess.client; // <-- Constructor
