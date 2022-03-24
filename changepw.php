<?php 
session_start();
include_once('lib/auth.php');
include_once('lib/nonce.php');
if (!auth()) {
    header('Location: /login.php', true, 302);
    exit();
}

?>
<html>
    <h2>Update Password</h2>
    <fieldset>
        <legend>Change Password</legend>
        <form id="prod_insert" method="POST" action="auth-process.php?action=<?php echo urlencode($action = 'change_pw');?>">
            <label for="login_email">E-mail: </label>
            <input type="text" id="login_email" name="femail" pattern="[a-zA-Z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{1,}$" title="Require: [chars]@[domain].[word]"> <br>
            <label for="login_opw">Old Password: </label>
            <input type="text" id="login_opw" name="fopw" pattern="^[a-zA-Z0-9!@#$%^&*_=+-]+$" title="Only letters, digits, !@#$%^&*_=+- allowed"> <br>
            <label for="login_npw">New Password: </label>
            <input type="text" id="login_npw" name="fnpw" pattern="^[a-zA-Z0-9!@#$%^&*_=+-]+$" title="Only letters, digits, !@#$%^&*_=+- allowed"> <br>
            <input type="hidden" name="nonce" value="<?php echo csrf_getNonce($action); ?>"/>
            <input type="submit">
        </form>
    </fieldset>
    <a href="/index.php">Back to Shop</a>
</html>