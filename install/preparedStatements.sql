DELIMITER $$

DROP PROCEDURE IF EXISTS `ChallengeUser`$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ChallengeUser`(
challengedUserID INT,
currentUserID INT,
startedGamePlayerUsername  VARCHAR(255),
gameID INT,
incorrectID INT,
alreadyChallengedPlayer INT
)
BEGIN

DECLARE TMP_ID INT DEFAULT 0;
DECLARE TMP_W_PLAYER INT DEFAULT 0;    
DECLARE TMP_B_PLAYER INT DEFAULT 0;
DECLARE startedGamePlayerUsername  VARCHAR(255); 

    SELECT `username` INTO startedGamePlayerUsername 
      FROM chess_users
      WHERE `user_id` = challengedUserID 
          AND `user_id` != currentUserID 
      LIMIT 1;

    IF startedGamePlayerUsername IS NOT NULL THEN

        SELECT `id` INTO TMP_ID 
          FROM `chess_games` 
          WHERE `whiteUserID` = currentUserID 
            AND `blackUserID` = challengedUserID 
            AND `outcome` = -1 
            AND `tournamentID` IS NULL
          LIMIT 1;

        -- here was bad NULL handling    
        IF TMP_ID IS NULL OR TMP_ID='' THEN

            SELECT `softwareID` INTO TMP_W_PLAYER
              FROM chess_users 
              WHERE `user_id`=currentUserID 
              LIMIT 1;

            SELECT `softwareID` INTO TMP_B_PLAYER 
              FROM chess_users 
              WHERE `user_id`=challengedUserID 
              LIMIT 1;

            INSERT INTO `chess_games` 
            (`tournamentID`, `whiteUserID`,`blackUserID`, `whitePlayerSoftwareID`,`blackPlayerSoftwareID`, `moveList`)
                     SELECT NULL, currentUserID, challengedUserID, TMP_W_PLAYER, TMP_B_PLAYER, "";           


            -- Get the id of the just inserted tuple
            -- This is MySQL specific. You might have to adjust this 
            -- line, if you want to port this to other databases
            SELECT LAST_INSERT_ID() INTO gameID;
        ELSE
            SET alreadyChallengedPlayer = 1;
            SET gameID = TMP_ID;
        END IF;
    ELSE
        SET incorrectID = 1;
    END IF;

    SELECT startedGamePlayerUsername, gameID, incorrectID, alreadyChallengedPlayer;
END$$

DELIMITER ;
