<?php

/******************************************************************************/
/* Table Names (constants)                                                    */
/******************************************************************************/
$table_prefix = 'chess_';
define('USERS_TABLE',                      $table_prefix.'users');
define('GAMES_TABLE',                      $table_prefix.'games');
define('TOURNAMENTS_TABLE',                $table_prefix.'tournaments');
define('TOURNAMENT_PLAYERS_TABLE',         $table_prefix.'tournamentPlayers');
define('SOFTWARE_TABLE',                   $table_prefix.'software');
define('SOFTWARE_USER_TABLE',              $table_prefix.'softwareUsers');
define('SOFTWARE_DEVELOPER_TABLE',         $table_prefix.'softwareDeveloper');
define('SOFTWARE_LANGUAGES_TABLE',         $table_prefix.'softwareLanguages');
define('LANGUAGES_TABLE',                  $table_prefix.'languages');
define('GAMES_THREEFOLD_REPETITION_TABLE', $table_prefix.'gamesThreefoldRepetition');
define('USERS_OPENID',                     $table_prefix.'userOpenID');

/******************************************************************************/
/* Table Column names                                                         */
/******************************************************************************/
define('USER_NAME_COLUMN',                 'user_name');

?>
