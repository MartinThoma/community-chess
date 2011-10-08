<?php
/**
 * start page. Lists all other pages.
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

require_once 'wrapper.inc.php';
require_once 'additional.inc.php';
require_once 'i18n.inc.php';

if (USER_ID === false) exit(_("Please <a href='login.wrapper.php'>login</a>"));
$t = new vemplator();
$t->assign('USER_ID', USER_ID);
checkSoftwareTableEntry(USER_ID);

/* Assign variables for i18n with gettext ******************************************/
$t->assign('title', _('About Community Chess'));
$t->assign('firstParagraph', _('Community Chess is hosted on '.
                              '<a href="http://code.google.com/p/community-chess/">'.
                              'code.google.com</a>. The latest version can be '.
                              'downloaded there and all documentation is in the '.
                              'wiki-section.'));
$t->assign('information', _('Information'));
$t->assign('loggedInWithID', _('Logged in with UserID '));
/* Print the html ******************************************************************/
echo $t->output('index.html');
?>
