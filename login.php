<!DOCTYPE html>
<html>
<body>
    <h3>Login Page</h3>
    <form method="post" action="auth-process.php?action=user_login">
        <label for="login_email">E-mail: </label>
        <input type="text" id="login_email" name="femail"> <br>
        <label for="login_pw">Password: </label>
        <input type="text" id="login_pw" name="fpw"> <br>
        <input type="submit">
    </form>
    <a href="/index.php">Back to Shop</a>
</body>
</html>