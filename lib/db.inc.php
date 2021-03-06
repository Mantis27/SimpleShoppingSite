<?php
function ierg4210_DB() {
	// connect to the database
	// TODO: change the following path if needed
	// Warning: NEVER put your db in a publicly accessible location
	$db = new PDO('sqlite:/var/www/cart.db');

	// enable foreign key support
	$db->query('PRAGMA foreign_keys = ON;');

	// FETCH_ASSOC:
	// Specifies that the fetch method shall return each row as an
	// array indexed by column name as returned in the corresponding
	// result set. If the result set contains multiple columns with
	// the same name, PDO::FETCH_ASSOC returns only a single value
	// per column name.
	$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

	return $db; // Return Database
}

function ierg4210_cat_fetchall() {
    // DB manipulation
    global $db;
    $db = ierg4210_DB();

    $q = $db->prepare("SELECT * FROM categories LIMIT 100;");
    if ($q->execute())
        return $q->fetchAll();
}

// Since this form will take file upload, we use the tranditional (simpler) rather than AJAX form submission.
// Therefore, after handling the request (DB insert and file copy), this function then redirects back to admin.html
function ierg4210_prod_insert() {
    // DB manipulation
    global $db;
    $db = ierg4210_DB();
    
    if (!preg_match('/^[\d\- ]+$/', $_POST['catid']))
        throw new Exception("invalid-catid");
    $_POST['catid'] = (int) $_POST['catid'];
    if (!preg_match('/^[\w\- ]+$/', $_POST['name']))
        throw new Exception("invalid-name");
    if (!preg_match('/^[\d\.]+$/', $_POST['price']))
        throw new Exception("invalid-price");
    //$_POST['price1'] = floatval($_POST['price1']);
    if (!preg_match('/^[\w\-\,\.\& ]+$/', $_POST['description']))
        throw new Exception("invalid-text");
    if (!preg_match('/^\d*$/', $_POST['stock']))
        throw new Exception("invalid-stock");
    $_POST['stock'] = (int) $_POST['stock'];
    
    if ($_FILES["file1"]["error"] == 0
        && $_FILES["file1"]["type"] == "image/jpeg"
        && mime_content_type($_FILES["file1"]["tmp_name"]) == "image/jpeg"
        && $_FILES["file1"]["size"] < 5000000) {
        $catid = $_POST["catid"];
        $catid = filter_var($catid, FILTER_SANITIZE_NUMBER_INT);
        if (!filter_var($catid, FILTER_VALIDATE_INT))
            throw new Exception("invalid-catid");
        $name = $_POST["name"];
        $name = filter_var($name, FILTER_SANITIZE_STRING);
        $price = $_POST["price"];
        $price = filter_var($price, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        if (!filter_var($price, FILTER_VALIDATE_FLOAT))
            throw new Exception("invalid-price");
        $desc = $_POST["description"];
        $desc = filter_var($desc, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_AMP);
        $stock = $_POST["stock"];
        $stock = filter_var($stock, FILTER_SANITIZE_NUMBER_INT);
        if (!filter_var($stock, FILTER_VALIDATE_INT))
            throw new Exception("invalid-stock");
        
        $sql="INSERT INTO products (catid, name, price, description, stock) VALUES (?, ?, ?, ?, ?)";
        $q = $db->prepare($sql);
        $q->bindParam(1, $catid, PDO::PARAM_INT);
        $q->bindParam(2, $name, PDO::PARAM_STR);
        $q->bindParam(3, $price, PDO::PARAM_STR);
        $q->bindParam(4, $desc, PDO::PARAM_STR);
        $q->bindParam(5, $stock, PDO::PARAM_INT);
        $q->execute();
        $lastId = $db->lastInsertId();
        
        // Note: Take care of the permission of destination folder (hints: current user is apache)
        if (move_uploaded_file($_FILES["file1"]["tmp_name"], "/var/www/html/Resources/Item_Photo/" . $lastId . ".jpg")) {
            // redirect back to original page; you may comment it during debug
            header('Location: admin.php');
            exit();
        }

    }
    // Only an invalid file will result in the execution below
    // To replace the content-type header which was json and output an error message
    header('Content-Type: text/html; charset=utf-8');
    echo 'Invalid file detected. <br/><a href="javascript:history.back();">Back to admin panel.</a>';
    exit();
}

// TODO: add other functions here to make the whole application complete
function ierg4210_cat_insert() {
    // DB manipulation
    global $db;
    $db = ierg4210_DB();

    if (!preg_match('/^[\w\- ]+$/', $_POST['name']))
        throw new Exception("invalid-name");

    $sql="INSERT INTO categories (catid, name) VALUES (NULL, ?)";
    $q = $db->prepare($sql);

    $name = $_POST["name"];
    $name = filter_var($name, FILTER_SANITIZE_STRING);

    $q->bindParam(1, $name, PDO::PARAM_STR);
    $q->execute();
    header('Location: admin.php');
    exit();
}
function ierg4210_cat_edit(){
    // Mainly update name
    global $db;
    $db = ierg4210_DB();

    if (!preg_match('/^\d*$/', $_POST['catid']))
        throw new Exception("invalid-catid");
    $_POST['catid'] = (int) $_POST['catid']; // turn into int
    if (!preg_match('/^[\w\- ]+$/', $_POST['name']))
        throw new Exception("invalid-name");

    $sql="UPDATE categories SET name = ? WHERE catid = ?";
    $q = $db->prepare($sql);

    $name = $_POST["name"];
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    $catid = $_POST["catid"];
    $catid = filter_var($catid, FILTER_SANITIZE_NUMBER_INT);
    if (!filter_var($catid, FILTER_VALIDATE_INT))
        throw new Exception("invalid-catid");

    $q->bindParam(1, $name, PDO::PARAM_STR);
    $q->bindParam(2, $catid, PDO::PARAM_INT);
    $q->execute();
    header('Location: admin.php');
    exit();
}
function ierg4210_cat_delete(){
    // DB manipulation
    global $db;
    $db = ierg4210_DB();
    
    if (!preg_match('/^\d*$/', $_POST['catid']))
        throw new Exception("invalid-catid");
    $_POST['catid'] = (int) $_POST['catid']; // turn into int

    $sql="DELETE FROM categories WHERE catid = ?";
    $q = $db->prepare($sql);

    $catid = $_POST["catid"];
    $catid = filter_var($catid, FILTER_SANITIZE_NUMBER_INT);
    if (!filter_var($catid, FILTER_VALIDATE_INT))
        throw new Exception("invalid-catid");

    $q->bindParam(1, $catid, PDO::PARAM_INT);
    $q->execute();
    header('Location: admin.php');
    exit();
}
function ierg4210_prod_delete_by_catid(){
    global $db;
    $db = ierg4210_DB();
    
    if (!preg_match('/^\d*$/', $_POST['pid']))
        throw new Exception("invalid-pid");
    $_POST['pid'] = (int) $_POST['pid']; // turn into int

    $sql="DELETE FROM products WHERE pid = ?";
    $q = $db->prepare($sql);
    $pid = $_POST["pid"];
    $pid = filter_var($pid, FILTER_SANITIZE_NUMBER_INT);
    if (!filter_var($pid, FILTER_VALIDATE_INT))
        throw new Exception("invalid-pid");
    $q->bindParam(1, $pid, PDO::PARAM_INT);
    if (unlink("/var/www/html/Resources/Item_Photo/".$pid.".jpg")) {
        $q->execute();
        header('Location: admin.php');
        exit();
    }
    header('Location: admin.php');
    exit();
}
function ierg4210_prod_fetchAll(){
    global $db;
    $db = ierg4210_DB();
    $q = $db->prepare("SELECT * FROM products;");
    if ($q->execute())
        return $q->fetchAll();
}
function ierg4210_prod_fetchOne(){}
function ierg4210_prod_edit(){
    // DB manipulation
    global $db;
    $db = ierg4210_DB();

    if (!preg_match('/^\d*$/', $_POST['pid']))
        throw new Exception("invalid-pid");
    $_POST['pid'] = (int) $_POST['pid']; // turn into int
    if (!preg_match('/^\d*$/', $_POST['catid']))
        throw new Exception("invalid-catid");
    $_POST['catid'] = (int) $_POST['catid'];
    if (!preg_match('/^[\w\- ]+$/', $_POST['name']))
        throw new Exception("invalid-name");
    if (!preg_match('/^[\d\.]+$/', $_POST['price']))
        throw new Exception("invalid-price");
    if (!preg_match('/^[\w\-\,\.\& ]+$/', $_POST['description']))
        throw new Exception("invalid-text");
    if (!preg_match('/^\d*$/', $_POST['stock']))
        throw new Exception("invalid-stock");
    $_POST['stock'] = (int) $_POST['stock'];

    $sql="DELETE FROM products WHERE pid = ?";
    $q = $db->prepare($sql);
    $pid = $_POST["pid"];
    $pid = filter_var($pid, FILTER_SANITIZE_NUMBER_INT);
    if (!filter_var($pid, FILTER_VALIDATE_INT))
        throw new Exception("invalid-pid");
    $q->bindParam(1, $pid, PDO::PARAM_INT);
    if (unlink("/var/www/html/Resources/Item_Photo/".$pid.".jpg")) {
        $q->execute(); //detele record

        if ($_FILES["file"]["error"] == 0
        && $_FILES["file"]["type"] == "image/jpeg"
        && mime_content_type($_FILES["file"]["tmp_name"]) == "image/jpeg"
        && $_FILES["file"]["size"] < 5000000) {
            $catid = $_POST["catid"];
            $catid = filter_var($catid, FILTER_SANITIZE_NUMBER_INT);
            if (!filter_var($catid, FILTER_VALIDATE_INT))
                throw new Exception("invalid-catid");
            $name = $_POST["name"];
            $name = filter_var($name, FILTER_SANITIZE_STRING);
            $price = $_POST["price"];
            $price = filter_var($price, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            if (!filter_var($price, FILTER_VALIDATE_FLOAT))
                throw new Exception("invalid-price");    
            $desc = $_POST["description"];
            $desc = filter_var($desc, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_AMP);
            $stock = $_POST["stock"];
            $stock = filter_var($stock, FILTER_SANITIZE_NUMBER_INT);
            if (!filter_var($stock, FILTER_VALIDATE_INT))
                throw new Exception("invalid-stock");
            
            $sql="INSERT INTO products (pid, catid, name, price, description, stock) VALUES (?, ?, ?, ?, ?, ?)";
            $q = $db->prepare($sql);
            $q->bindParam(1, $pid, PDO::PARAM_INT);
            $q->bindParam(2, $catid, PDO::PARAM_INT);
            $q->bindParam(3, $name, PDO::PARAM_STR);
            $q->bindParam(4, $price, PDO::PARAM_STR);
            $q->bindParam(5, $desc, PDO::PARAM_STR);
            $q->bindParam(6, $stock, PDO::PARAM_INT);
            $q->execute();
            
            // Note: Take care of the permission of destination folder (hints: current user is apache)
            if (move_uploaded_file($_FILES["file"]["tmp_name"], "/var/www/html/Resources/Item_Photo/" . $pid . ".jpg")) {
                // redirect back to original page; you may comment it during debug
                header('Location: admin.php');
                exit();
            }
        }
    }
    // Only an invalid file will result in the execution below
    // To replace the content-type header which was json and output an error message
    header('Content-Type: text/html; charset=utf-8');
    echo 'Invalid file detected. <br/><a href="javascript:history.back();">Back to admin panel.</a>';
    exit();
    
}
function ierg4210_prod_delete(){ // no use yet
    global $db;
    $db = ierg4210_DB();
    if (!preg_match('/^\d*$/', $_POST['pid']))
        throw new Exception("invalid-pid");
    $_POST['pid'] = (int) $_POST['pid']; // turn into int
    $sql="DELETE FROM products WHERE pid = ?";
    $q = $db->prepare($sql);
    $pid = $_POST["pid"];
    $pid = filter_var($pid, FILTER_SANITIZE_NUMBER_INT);
    if (!filter_var($pid, FILTER_VALIDATE_INT))
        throw new Exception("invalid-pid");
    $q->bindParam(1, $pid);
    if (unlink("/var/www/html/Resources/Item_Photo/".$pid.".jpg")) {
        $q->execute(); //detele record
    }
}
