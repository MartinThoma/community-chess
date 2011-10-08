<?php
/**
 * Define all constants which might be adjusted.
 *
 * PHP Version 5
 *
 * @category Web_Services
 * @package  Community-chess
 * @author   Martin Thoma <info@martin-thoma.de>
 * @license  http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version  SVN: <svn_id>
 * @link     http://code.google.com/p/community-chess/
 */

/***********************************************************************************/
/* Table Names (constants)                                                         */
/***********************************************************************************/
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

/***********************************************************************************/
/* Table Column names                                                              */
/***********************************************************************************/
define('USER_NAME_COLUMN',                 'user_name');

/***********************************************************************************/
/* Others                                                                          */
/***********************************************************************************/
define('HOST',                             'localhost');

?>
