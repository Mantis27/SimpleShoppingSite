<?php
header('Content-Type: application/json');

function ierg4210_DB() {
	$db = new PDO('sqlite:/var/www/cart.db');
	$db->query('PRAGMA foreign_keys = ON;');
	$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

	return $db; // Return Database
}

function ierg4210_user_login() {
    $login_result = false;
    global $db;
    $db = ierg4210_DB();
    if (empty($_POST['femail']) || empty($_POST['fpw']) 
        || !preg_match("/^[\w=+\-\/][\w='+\-\/\.]*@[\w\-]+(\.[\w\-]+)*(\.[\w]{2,6})$/", $_POST['femail'])
        || !preg_match("/^[\w@#$%\^\&\*\-]+$/", $_POST['fpw'])) {
        //throw new Exception("Wrong Credentials");
        header('Location: login.php', true, 302);
        exit();
    }
        

    $sql="SELECT email, salt, pw, adminflag FROM users WHERE email=?";
    $q = $db->prepare($sql);
    $email = $_POST["femail"];
    $o_pw = $_POST["fpw"];
    $q->bindParam(1, $email);
    if ($q->execute()) {
        $result = $q->fetch();
        $login_result = false;
        if (hash_hmac('sha256', $o_pw, $result["SALT"]) == $result["PW"]) {
            $login_result = true;
        } 
    }
    if ($login_result) {
        // set cookie and token
        $exp = time() + 3600 * 24 * 3;
        $token = array(
            'em'=>$result["EMAIL"],
            'exp'=>$exp,
            'k'=> hash_hmac('sha256', $exp.$result["PW"], $result["SALT"])
        );
        setcookie('auth', json_encode($token), $exp, '/', '', true, true);
        $_SESSION['auth'] = $token;
        session_regenerate_id();
        if ($result["ADMINFLAG"] == 1) {
            // redirect
            header('Location: admin.php', true, 302);
            exit();
        }
        else {
            header('Location: index.php', true, 302);
            exit();
        }

    }
    else {
        //throw new Exception("Wrong Cred");
        header('Location: login.php', true, 302);
        exit();
    }
}

function ierg4210_change_pw() {
    $change_result = false;
    $login_result = false;
    global $db;
    $db = ierg4210_DB();
    if (empty($_POST['femail']) || empty($_POST['fopw']) || empty($_POST['fnpw']) 
        || !preg_match("/^[\w=+\-\/][\w='+\-\/\.]*@[\w\-]+(\.[\w\-]+)*(\.[\w]{2,6})$/", $_POST['femail'])
        || !preg_match("/^[\w@#$%\^\&\*\-]+$/", $_POST['fopw'])
        || !preg_match("/^[\w@#$%\^\&\*\-]+$/", $_POST['fnpw'])) {
        //throw new Exception("Wrong Credentials");
        header('Location: changepw.php', true, 302);
        exit();
    }
    $sql="SELECT email, salt, pw, adminflag FROM users WHERE email=?";
    $q = $db->prepare($sql);
    $email = $_POST["femail"];
    $o_pw = $_POST["fopw"];
    $q->bindParam(1, $email);
    if ($q->execute()) {
        $result = $q->fetch();
        $login_result = false;
        if (hash_hmac('sha256', $o_pw, $result["SALT"]) == $result["PW"]) {
            $login_result = true;
        } 
    }
    if ($login_result) {
        // old pw is good
        $sql="UPDATE users SET salt=?, pw=? WHERE email=?";
        $q = $db->prepare($sql);
        $email = $_POST["femail"];
        $n_pw = $_POST["fnpw"];
        $salt = mt_rand();
        $ns_pw = hash_hmac('sha256', $n_pw, $salt);
        $q->bindParam(1, $salt);
        $q->bindParam(2, $ns_pw);
        $q->bindParam(3, $email);
        if ($q->execute()) {
            $change_result = true;
        }
        if ($change_result) {
            unset($_SESSION['auth']);
            setcookie('auth', "Testing", time()-3600, '/', '', true, true);
            header('Location: login.php', true, 302);
            exit();
        }
        else {
            //throw new Exception("Failed new pw");
            header('Location: changepw.php', true, 302);
            exit();
        }
    }
    else {
        //throw new Exception("Wrong Cred");
        header('Location: changepw.php', true, 302);
        exit();
    }
}

// input validation
if (empty($_REQUEST['action']) || !preg_match('/^\w+$/', $_REQUEST['action'])) {
	echo json_encode(array('failed'=>'undefined'));
	exit();
}

try {

	if (($returnVal = call_user_func('ierg4210_' . $_REQUEST['action'])) === false) {
		if ($db && $db->errorCode()) 
			error_log(print_r($db->errorInfo(), true));
		echo json_encode(array('failed'=>'1'));
	}
	echo 'while(1);' . json_encode(array('success' => $returnVal));
} catch(PDOException $e) {
	error_log($e->getMessage());
	echo json_encode(array('failed'=>'error-db'));
} catch(Exception $e) {
	echo 'while(1);' . json_encode(array('failed' => $e->getMessage()));
}
?>