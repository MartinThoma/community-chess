#!/usr/bin/python
# -*- coding: utf-8 -*-

from PythonClient import *

client = ChessClient('test', 'test')

# Which moves are possible for the piece at (0|3)?
print client.getPossibleMoves(client.board[0][6])
