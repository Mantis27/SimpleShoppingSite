<?php 
$db = new PDO('sqlite:/var/www/cart.db');

if (isset($_POST["orderList"])) {
    $orderList = $_POST["orderList"];    

    $salt = mt_rand();
    $digest = hash_hmac('sha256', $orderList, $salt);
    $status = "PENDING";
    
    $sql = "INSERT INTO orders (email, digest, salt, jsonstring, status, txnid) VALUES (?, ?, ?, ?, ?, 'none');";
    $q = $db->prepare($sql);

    $email = filter_var($_POST["userEmail"], FILTER_SANITIZE_STRING);
    $q->bindParam(1, $email, PDO::PARAM_STR);
    $q->bindParam(2, $digest, PDO::PARAM_STR);
    $q->bindParam(3, $salt, PDO::PARAM_STR);
    $jsonstring = filter_var($orderList, FILTER_SANITIZE_STRING);
    $q->bindParam(4, $jsonstring, PDO::PARAM_STR);
    $q->bindParam(5, $status, PDO::PARAM_STR);


    $q->execute();
    $orderID = strval($db->lastInsertId());

    echo $digest." ".$orderID;
    exit();
}
?>