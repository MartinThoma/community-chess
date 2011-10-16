<?php
/**
 * get a list of links to players you can challenge
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

require_once '../wrapper.inc.php';
require_once '../additional.inc.php';
require_once '../i18n.inc.php';

if (USER_ID === false) exit(_("Please <a href='../login.wrapper.php'>login</a>"));
$t = new vemplator();
$t->assign('USER_ID', USER_ID);


set_include_path('../templates/challenges');
echo $t->output('tooManyPieces.html');
?>
