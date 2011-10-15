#!/usr/bin/python
# -*- coding: utf-8 -*-

class ChessPiece(object):
    """ This is a super class for chess pieces """
    def __init__(self, letter, value, x, y):
        self.letter = letter
        self.color = self.getColor()
        self.value = value
        self.x     = x
        self.y     = y

    def __str__(self):
        return self.letter

    def __repr__(self):
        pythonRepresentation  = ("Piece:\t%s\n" % self.letter)
        pythonRepresentation += ("Value:\t%i\n" % self.value)
        pythonRepresentation += ("Position:\t(%i|%i)\n" % (self.x, self.y))
        return pythonRepresentation
        #return self.letter

    def getColor(self):
        if self.letter.isupper():
            color = 'white'
        else:
            color = 'black'
        self.color = color

    def standardMoves(self):
        """ Get a list of vectors for the standard moves. """
        return []

    def captureMoves(self):
        """ Get a list of vectors for the caputure moves. """
        return []

class Pawn(ChessPiece):
    """ This is a pawn class """
    def __init__(self, letter, x, y):
        ChessPiece.__init__(self, letter, 1, x, y)

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

class King(ChessPiece):
    """ This is a king class """
    def __init__(self, letter, x, y):
        ChessPiece.__init__(self, letter, 1, x, y)
        self.moves = [(0,1),(1,0),(0,-1),(-1,0),(1,1),(-1,1),(1,-1),(-1,-1)]

    def standardMoves(self):
        return self.moves

    def captureMoves(self):
        return self.moves

class Knight(ChessPiece):
    """ This is a king class """
    def __init__(self, letter, x, y):
        ChessPiece.__init__(self, letter, 1, x, y)
        self.moves = [(1,2),(-1,2),(1,-2),(-1,-2),(2,1),(-2,1),(2,-1),(-2,-1)]

    def standardMoves(self):
        return self.moves

    def captureMoves(self):
        return self.moves
