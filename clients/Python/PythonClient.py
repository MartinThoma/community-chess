#!/usr/bin/python
# -*- coding: utf-8 -*-

from ChessPieces import *
import urllib, re, time

class ChessClient(object):
    """ This is a Community Chess Client Class """

    def __init__(self, username, password):
        """ Constructor """
        self.baseUrl = "http://localhost/community-chess/xhrframework.php"
        self.username = username
        self.password = password
        self.cookie = 'PHPSESSID=' + self.login()
        self.currentGames = self.getCurrentGames()
        if len(self.currentGames) == 0:
            idList = self.getPlayerIDs()
            self.currentGameID = self.challengePlayer(idList[0])
            self.currentGames.append(self.currentGameID)
        else:
            self.currentGameID = self.currentGames[0]
        self.getBoard()
        self.myColor = self.whoAmI()
        self.colorsTurn = self.whoseTurnIsIt()
        self.myTurn = (self.myColor == self.colorsTurn)
        self.gameLoop()

    def __repr__(self):
        pythonRepresentation  = ("Community Chess Client class\n")
        pythonRepresentation += ("Board:\n")
        for y in xrange(0,8):
            row = []
            for x in xrange(0, 8):
                row.append(str(self.board[x][y]))
            pythonRepresentation += "%i. %s\n" % (y+1, str(row)) #("%s\n" % str(row))

        return pythonRepresentation

    def login(self):
        """ Login with the given credentials. Save the cookie. """
        url = self.baseUrl + '?action=login&username=' + self.username + \
              '&password=' + self.password
        u = urllib.URLopener();x = u.open(url)
        return x.read()

    def sendRequestWithCookie(self, getstring):
        """ This method is only for usage within the Client Class """
        url = self.baseUrl + getstring
        u = urllib.URLopener();u.addheader('Cookie',self.cookie);x = u.open(url)
        content = x.read()
        return content

    def parseList(self, content):
        """ This helper method parses a string to a list. """
        if '::' in content:
            liste = content.split('::')
        else:
            if int(content) != None:
                liste = [content]
            else:
                liste = []
        return liste


    def getCurrentGames(self):
        """ Get a list with all current Game-IDs. """
        content = self.sendRequestWithCookie('?action=listCurrentGames')
        liste   = self.parseList(content)
        return liste

    def getPlayerIDs(self):
        """ Get a list with all players you can challenge. """
        content = self.sendRequestWithCookie('?action=getPlayerIDs')
        liste   = self.parseList(content)
        return liste

    def challengePlayer(self, playerID):
        """ Challenge the player with the ID playerID. """
        content = self.sendRequestWithCookie('?action=challengeUser&user_id=' + playerID)
        return content

    def submitMove(self, move):
        """ Challenge the player with the ID playerID. """
        query = "?gameID=%s&move=%s" % (self.currentGameID, move)
        content = self.sendRequestWithCookie(query)
        return content

    def getBoard(self):
        url = self.baseUrl + '?gameID=' + self.currentGameID + '&action=getBoard'
        u = urllib.URLopener();u.addheader('Cookie',self.cookie);x = u.open(url)
        content = x.read()
        board =[[None for j in xrange(0,8)] for i in xrange(0,8)]
        for y in xrange(0, 8):
            for x in xrange(0, 8):
                pos = x + y*8
                letter = content[pos:(pos+1)]
                if content[pos:(pos+1)] in ['p', 'P']:
                    board[x][y] = Pawn(letter, x, y)
                elif content[pos:(pos+1)] in ['k', 'K']:
                    board[x][y] = King(letter, x, y)
                elif content[pos:(pos+1)] in ['n', 'N']:
                    board[x][y] = Knight(letter, x, y)
                else:
                    board[x][y] = ChessPiece(letter, 0, x, y)
        self.board = board
        return board

    def whoAmI(self):
        url = self.baseUrl + '?gameID=' + self.currentGameID + '&action=whoAmI'
        u = urllib.URLopener();u.addheader('Cookie',self.cookie);x = u.open(url)
        content = x.read()
        return content

    def whoseTurnIsIt(self):
        url = self.baseUrl + '?gameID=' + self.currentGameID + '&action=whoseTurnIsIt'
        u = urllib.URLopener();u.addheader('Cookie',self.cookie);x = u.open(url)
        content = x.read()
        return content

    def isEnemy(self, x, y):
        if self.board[x][y] == '0':
            return False
        else:
            if self.myColor == 'white':
                return self.board[x][y].letter.islower()
            else:
                return self.board[x][y].letter.isupper()

    def isOwn(self, x, y):
        if self.board[x][y] == '0':
            return False
        else:
            if self.myColor == 'white':
                return self.board[x][y].letter.isupper()
            else:
                return self.board[x][y].letter.islower()

    def isEmpty(self, x, y):
        return self.board[x][y].letter == '0'

    def getPossibleMoves(self, piece):
        """ Get a list of vectors for all possible moves. """
        moveVectors = []

        standard = piece.standardMoves()
        for move in standard:
            if (piece.x + move[0]) < 8 and (piece.y + move[1]) < 8 \
                and self.isEmpty(move[0] + piece.x, move[1] + piece.y):
                moveVectors.append(move)

        capture = piece.captureMoves()
        for move in capture:
            if (piece.x + move[0]) < 8 and (piece.y + move[1]) < 8 and \
                self.isEnemy(move[0], move[1]):
                moveVectors.append(move)

        return moveVectors

    def makeMove(self):
        for y, row in enumerate(self.board):
            for x, element in enumerate(row):
                if self.isOwn(x, y):
                    for vector in self.getPossibleMoves(self.board[x][y]):
                        move = "%i%i%i%i" % (x+1, y+1, x+vector[0]+1, y+vector[1]+1)
                        content = self.submitMove(move)
                        if not ('ERROR' in content):
                            return True
        return False
                        

    def gameLoop(self):
        while True:
            while self.myTurn == False:
                print("Not your turn. Wait a second")
                time.sleep(1)
                self.colorsTurn = self.whoseTurnIsIt()
                self.myTurn = (self.myColor == self.colorsTurn)
            self.getBoard()
            if self.makeMove():
                print("Moved.")


