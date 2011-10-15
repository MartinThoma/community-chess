#!/usr/bin/python
# -*- coding: utf-8 -*-

from PythonClient import *

client = ChessClient('test', 'test')

# Which moves are possible for the piece at (0|3)?
print client.board[0][3].getPossibleMoves(client)
