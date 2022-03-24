<?php
function newDB() {
	$db = new PDO('sqlite:/var/www/cart.db');
	$db->query('PRAGMA foreign_keys = ON;');
	$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	return $db; // Return Database
}

function auth() {
    if (!empty($_SESSION['auth']))
        return $_SESSION['auth']['em'];
    if (!empty($_COOKIE['auth'])) {
        if ($t = json_decode(stripslashes($_COOKIE['auth']), true)) {
            if (time() > $t['exp'])
                return false;
            $db = newDB();
            $q = $db->prepare('SELECT * FROM users WHERE email=?');
            $q->execute(array($t['em']));
            if ($r = $q ->fetch()) {
                $realk = hash_hmac('sha256', $t['exp'].$r['PW'], $r['SALT']);
                if ($realk == $t['k']) {
                    $_SESSION['auth'] = $t;
                    return $t['em'];
                }
            }
        }
    }
    return false;
}

function authAdmin() {
    if (!empty($_SESSION['auth'])) {
        // is auth-ed, but is he admin?
        $db = newDB();
        $q = $db->prepare('SELECT adminflag FROM users WHERE email=?');
        $q->execute(array($_SESSION['auth']['em']));
        if ($r = $q ->fetch()) {
            if ($r['ADMINFLAG'] == 1) {
                // is admin!
                return $_SESSION['auth']['em'];
            }
        }
    }
    if (!empty($_COOKIE['auth'])) {
        if ($t = json_decode(stripslashes($_COOKIE['auth']), true)) {
            if (time() > $t['exp'])
                return false;
            $db = newDB();
            $q = $db->prepare('SELECT * FROM users WHERE email=?');
            $q->execute(array($t['em']));
            if ($r = $q ->fetch()) {
                $realk = hash_hmac('sha256', $t['exp'].$r['PW'], $r['SALT']);
                if ($realk == $t['k']) {
                    // token not changed
                    if ($r['ADMINFLAG'] == 1) {
                        $_SESSION['auth'] = $t;
                        return $t['em'];
                    }
                }
            }
        }
    }
    return false;
}
?>