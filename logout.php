<?php 
session_start();
include_once('lib/auth.php');
if (!auth()) {
    header('Location: /login.php', true, 302);
    exit();
}
unset($_SESSION['auth']);
setcookie('auth', "Testing", time()-3600, '/', '', true, true);
header('Location: /login.php', true, 302);
exit();
?>