#!/usr/bin/python
# -*- coding: utf-8 -*-

class ChessPiece(object):
    """ This is a super class for chess pieces """
    def __init__(self, client, piece, color, value, x, y):
        self.piece = piece
        self.color = color
        self.value = value
        self.x     = x
        self.y     = y

    def standardMoves(self):
        """ Get a list of vectors for the standard moves. """
        pass

    def captureMoves(self):
        """ Get a list of vectors for the caputure moves. """
        pass

    def getPossibleMoves(self, client):
        """ Get a list of vectors for all possible moves. """
        moveVectors = []

        standard = self.standardMoves()
        for move in standard:
            if (self.x + move[0]) < 8 and (self.y + move[1]) < 8:
                moveVectors.append(move)

        capture = self.captureMoves()
        for move in capture:
            if (self.x + move[0]) < 8 and (self.y + move[1]) < 8 and \
               client.isEnemy(move[0], move[1]):
                moveVectors.append(move)

        return moveVectors

class Pawn(ChessPiece):
    """ This is a pawn class """
    def __init__(self, client, color, x, y):
        ChessPiece.__init__(self, client, 'pawn', color, 1, x, y)
        if color == 'P':
            color = 'white'
        else:
            color = 'black'

    def standardMoves(self):
        if self.color == 'white':
            return [(0,1)]
        else:
            return [(0,-1)]

    def captureMoves(self):
        if self.color == 'white':
            return [(1,1), (-1,1)]
        else:
            return [(1,-1), (-1,-1)]

