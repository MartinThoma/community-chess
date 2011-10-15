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

    def login(self):
        """ Login with the given credentials. Save the cookie. """
        url = self.baseUrl + '?action=login&username=' + self.username + \
              '&password=' + self.password
        u = urllib.URLopener();x = u.open(url)
        return x.read()

    def getCurrentGames(self):
        """ Get a list with all current Game-IDs. """
        url = self.baseUrl + '?action=listCurrentGames'
        u = urllib.URLopener();u.addheader('Cookie',self.cookie);x = u.open(url)
        content = x.read()
        if '::' in content:
            liste = content.split('::')
        else:
            if int(content) != None:
                liste = [content]
            else:
                liste = []
        return liste

    def getPlayerIDs(self):
        """ Get a list with all players you can challenge. """
        url = self.baseUrl + '?action=getPlayerIDs'
        u = urllib.URLopener();u.addheader('Cookie',self.cookie);x = u.open(url)
        content = x.read()
        if '::' in content:
            liste = content.split('::')
        else:
            if int(content) != None:
                liste = [content]
            else:
                liste = []
        return liste

    def challengePlayer(self, playerID):
        """ Challenge the player with the ID playerID. """
        url = self.baseUrl + '?action=challengeUser&user_id=' + playerID
        u = urllib.URLopener();u.addheader('Cookie',self.cookie);x = u.open(url)
        return x.read()

    def getBoard(self):
        url = self.baseUrl + '?gameID=' + self.currentGameID + '&action=getBoard'
        u = urllib.URLopener();u.addheader('Cookie',self.cookie);x = u.open(url)
        content = x.read()
        board =[[None for j in xrange(0,8)] for i in xrange(0,8)]
        for y in xrange(0, 8):
            for x in xrange(0, 8):
                pos = x + y*8
                if content[pos:(pos+1)] in ['p', 'P']:
                    board[x][y] = Pawn(self, content[pos:(pos+1)], x, y)
                else:
                    board[x][y] = content[pos:(pos+1)]
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
            return self.board[x][y].isupper()

    def gameLoop(self):
        while self.myTurn == False:
            print("Not your turn. Wait a second")
            time.sleep(1)
            self.colorsTurn = self.whoseTurnIsIt()
            self.myTurn = (self.myColor == self.colorsTurn)
        print self.getBoard()


