<?php 
$db = new PDO('sqlite:/var/www/cart.db');

if (isset($_POST["query_order"])) {
    $query_amount = $_POST["query_order"];    
    
    $sql = "SELECT * FROM orders ORDER BY OID DESC LIMIT ?;";
    $q = $db->prepare($sql);
    $q->bindParam(1, $query_amount, PDO::PARAM_INT);
    $q->execute();
    $res = $q->fetchAll();

    $result = "<p>Table showing last $query_amount orders.</p>";
    $result .= "<table id='ordertable_table'><tr><th>oid</th><th>user</th><th>jsonstring</th><th>status</th><th>txnid</th></tr>";
    foreach ($res as $value) {
        $oid = $value["OID"];
        $user = $value["EMAIL"];
        $jsonstring = $value["JSONSTRING"];
        $status = $value["STATUS"];
        $txnid = $value["TXNID"];

        $result .= "<tr><td>$oid</td><td>$user</td><td>$testingg</td><td>$status</td><td>$txnid</td></tr>";
    }


    $result .= "</table>";
    echo $result;
    exit();
}
?>