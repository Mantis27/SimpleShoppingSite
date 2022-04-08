<?php 
session_start();
include_once('lib/auth.php');
include_once('lib/nonce.php');
$auth_email = auth();
if (!$auth_email) {
    header('Location: /login.php', true, 302);
    exit();
}

?>
<html>
    <head>
        <link rel="stylesheet" href="stylefortable.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <title>User Page</title>
    </head>
    <body>
        <a href="/index.php">Back to Shop</a>
        <div id="ordertable"></div>
        <input id="hid_user" value="<?php echo htmlspecialchars($auth_email)?>" type="hidden"/>
        <script src="/lib/user_portal_script.js"></script>
    </body>
</html>