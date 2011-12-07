<?php
/**
 * let the user login. The whole file can get replaced by your login routine.
 * Other files in this project link to this file, so you should replace it with 
 * a redirection
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

if (!isset($_SESSION)) session_start();

require_once 'wrapper.inc.php';
require_once 'i18n.inc.php';
require_once 'external/lightOpenID/openid.php';

/* OpenID **************************************************************************/
/** This function gives a random string
 *
 * @param int $length The length of the random string
 *
 * @return string a random string with $length characters
 */
function getRandomString($length = 5)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $string     = ''; 
    for ($i = 0; $i < $length; $i++) {
        $string .= $characters[rand(0, strlen($characters))];
    }
    return $string;
}

try {
    $openid           = new LightOpenID(HOST);
    $openid->required = array('contact/email');
    $openid->optional = array('namePerson/friendly', 'namePerson');
    if (!$openid->mode) {
        if (isset($_POST['openid_identifier'])) {
            // e.g. http://martin-thoma.blogspot.com
            $openid->identity = $_POST['openid_identifier']; 
            header('Location: ' . $openid->authUrl());
        }
    } elseif ($openid->mode == 'cancel') {
        exit("ERROR: You've aborted the OpenID login process.");
    } else {
        if ($openid->validate()) {
            $stmt = $conn->prepare("SELECT user_id FROM `".USERS_OPENID."` ".
                                   "WHERE `OpenID` = :url LIMIT 1");
            $stmt->bindValue(':url', $openid->identity, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Is the OpenID already in the database?
            if ($result == false) {
                $openIDurl = $openid->identity;

                // register the user
                $attributes = $openid->getAttributes();
                $password   = getRandomString();

                $stmt  = $conn->prepare('INSERT INTO `'.USERS_TABLE.'` '.
                    '(`user_name`, `user_password`, `user_email`) VALUES '.
                    '(:uname, :upass, :uemail)');
                $uname = getRandomString();
                $stmt->bindValue(":uname", $uname);
                $stmt->bindValue(":upass", md5($password));
                $stmt->bindValue(":uemail", $attributes['contact/email']);
                $stmt->execute();

                // get ID
                $stmt = $conn->prepare('SELECT `user_id` '.
                        'FROM `'.USERS_TABLE.'` '.
                        'WHERE `user_name` = :uname AND '.
                        '`user_password` = :upass AND `user_email` = :uemail '.
                        'LIMIT 1');
                $stmt->bindValue(":uname", $uname);
                $stmt->bindValue(":upass", md5($password));
                $stmt->bindValue(":uemail", $attributes['contact/email']);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $id     = $result['user_id'];

                // set his OpenID
                $stmt = $conn->prepare('INSERT INTO `'.USERS_OPENID.'` '.
                    '(`user_id`, `OpenID`) VALUES '.
                    '(:uid, :openid)');
                $stmt->bindValue(":uid", $id);
                $stmt->bindValue(":openid", $openIDurl);
                $stmt->execute();

                // log the user in
                $_SESSION['user_id'] = ''.$id;
                header('Location: index.php');
            } else {
                $_SESSION['user_id'] = $result['user_id'];
                header('Location: index.php');
            }
        } else {
            exit('ERROR: Please login at your identity provider.');
        }
    }
} catch(ErrorException $e) {
    exit($e->getMessage());
}
/* /OpenID *************************************************************************/



$t = new vemplator();

if (isset($_POST['user_name'])) {
    login($_POST['user_name'], $_POST['user_password']);
}

/* Assign variables for i18n with gettext ******************************************/
$t->assign('username', _('username'));
$t->assign('password', _('password'));

/* Print the html ******************************************************************/
echo $t->output('login.html');
?>
