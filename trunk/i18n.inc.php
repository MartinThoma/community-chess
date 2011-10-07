<?php
/**
 * This script tries to detect the prefered language by the header which is sent by
 * the browser.
 * If non of the currently supported languages is detected, it will show the english
 * version.
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

if (stristr($_SERVER["HTTP_ACCEPT_LANGUAGE"], 'de') !== false) {
    $language = "de_DE";
} else {
    $language = "en_BR";
}

bindtextdomain('messages', './i18n/');
textdomain('messages');
setlocale(LC_ALL, $language);
putenv("LANG=".$language);

?>
