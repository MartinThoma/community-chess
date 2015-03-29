<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="utf-8" />
  <meta name="description" content="You can sign in on this page" />
  <title>Login - Community Chess</title> 
  <link rel="stylesheet" type="text/css" href="styling/default.css" />
  <link rel="icon" href="favicon.ico" type="image/x-icon" />
  <!-- Simple OpenID Selector -->
  <link type="text/css" rel="stylesheet" href="external/openid-selector/css/openid.css" />
  <script type="text/javascript" src="external/jquery-1.6.4.min.js"></script>
  <script type="text/javascript" src="external/openid-selector/js/openid-jquery.js"></script>
  <script type="text/javascript" src="external/openid-selector/js/openid-en.js"></script>
  <script type="text/javascript">
    $(document).ready(function() {
        openid.init('openid_identifier');
        //openid.setDemoMode(true); //Stops form submission for client javascript-only test purposes
    });
  </script>
  <!-- /Simple OpenID Selector -->
</head>
<body>
    <div id="content">
    <form method="post">
        <p>
            <label for="username">{username}:</label>
            <input type="text" name="username" />
        </p>
        <p>
            <label for="password">{password}:</label>
            <input type="password" name="password" />
        </p>
        <input type="submit"/>
    </form>


	<!-- Simple OpenID Selector -->
	<form action="login.wrapper.php" method="post" id="openid_form">
		<input type="hidden" name="action" value="verify" />
		<fieldset>
			<legend>Sign-in or Create New Account</legend>
			<div id="openid_choice">
				<p>Please click your account provider:</p>
				<div id="openid_btns"></div>
			</div>
			<div id="openid_input_area">
				<input id="openid_identifier" name="openid_identifier" type="text" value="http://" />
				<input id="openid_submit" type="submit" value="Sign-In"/>
			</div>
			<noscript>
				<p>OpenID is service that allows you to log-on to many different websites using a single indentity.
				Find out <a href="http://openid.net/what/">more about OpenID</a> and <a href="http://openid.net/get/">how to get an OpenID enabled account</a>.</p>
			</noscript>
		</fieldset>
	</form>
	<!-- /Simple OpenID Selector -->

    </div>
</body>
</html>
