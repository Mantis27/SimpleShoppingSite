<?php 
$db = new PDO('sqlite:/var/www/cart.db');

if (isset($_POST["query_order"])) {
    $query_amount = $_POST["query_order"];
    if (isset($_POST["query_user"])) {
        $query_user = $_POST["query_user"];
        $sql = "SELECT * FROM orders WHERE EMAIL=? ORDER BY OID DESC LIMIT ?;";
        $q = $db->prepare($sql);
        $query_user = filter_var($query_user, FILTER_SANITIZE_STRING);
        $q->bindParam(1, $query_user, PDO::PARAM_STR);
        $q->bindParam(2, $query_amount, PDO::PARAM_INT);
        $q->execute();
    }
    else {
        $sql = "SELECT * FROM orders ORDER BY OID DESC LIMIT ?;";
        $q = $db->prepare($sql);
        $q->bindParam(1, $query_amount, PDO::PARAM_INT);
        $q->execute();
    }
    $res = $q->fetchAll();

    $result = "<p>Table showing last $query_amount orders.</p>";
    $result .= "<table id='ordertable_table'><tr><th>OrderID</th><th>User</th><th>Items</th><th>Status</th><th>txnID</th></tr>";
    foreach ($res as $value) {
        $oid = $value["OID"];
        $user = $value["EMAIL"];
        $jsonstring = $value["JSONSTRING"];
        $status = $value["STATUS"];
        $txnid = $value["TXNID"];

        $item_result = "<ul>";
        $jsonobj = json_decode($jsonstring, true);
        $total_cost = $jsonobj["purchase_units"][0]["amount"]["value"];
        $item_arr = $jsonobj["purchase_units"][0]["items"];
        foreach ($item_arr as $item) {
            $item_name = $item["name"];
            $item_quan = $item["quantity"];
            $item_cost = $item["unit_amount"]["value"];
            $item_totcost = intval($item_quan) * floatval($item_cost);
            $item_result .= "<li>\"$item_name\" quantity:$item_quan price:$item_cost total:$item_totcost</li>";
        }
        $item_result .= "<li>Total: $total_cost</li></ul>";
        
        $result .= "<tr><td>$oid</td><td>$user</td><td>$item_result</td><td>$status</td><td>$txnid</td></tr>";
    }


    $result .= "</table>";
    echo $result;
    exit();
}
?>