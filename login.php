<!DOCTYPE html>
<?php
session_start();
include_once('lib/nonce.php');
?>
<html>
<body>
    <h3>Login Page</h3>
    <form method="post" action="auth-process.php?action=<?php echo ($action = 'user_login');?>">
        <label for="login_email">E-mail: </label>
        <input type="text" id="login_email" name="femail" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"> <br>
        <label for="login_pw">Password: </label>
        <input type="text" id="login_pw" name="fpw"> <br>
        <input type="hidden" name="nonce" value="<?php echo csrf_getNonce($action); ?>"/>
        <input type="submit">
    </form>
    <a href="/index.php">Back to Shop</a>
</body>
</html>