<?php
function newDB() {
	$db = new PDO('sqlite:/var/www/cart.db');
	$db->query('PRAGMA foreign_keys = ON;');
	$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	return $db; // Return Database
}

function auth() {
    if (!empty($_SESSION['s4210']))
        return $_SESSION['s4210']['em'];
    if (!empty($_COOKIE['s4210'])) {
        if ($t = json_decode(stripslashes($_COOKIE['s4210']), true)) {
            if (time() > $t['exp'])
                return false;
            $db = newDB();
            $q = $db->prepare('SELECT * FROM users WHERE email=?');
            $q->execute(array($t['em']));
            if ($r = $q ->fetch()) {
                $realk = hash_hmac('sha256', $t['exp'].$r['PW'], $r['SALT']);
                if ($realk == $t['k']) {
                    $_SESSION['s4210'] = $t;
                    return $t['em'];
                }
            }
        }
    }
    return false;
}
?>