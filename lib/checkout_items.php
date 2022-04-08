<?php 
$db = new PDO('sqlite:/var/www/cart.db');

if (isset($_POST["itemID"])) {
    $itemID = $_POST["itemID"];    

    // is a pid
    $sql = "SELECT name, price FROM products WHERE pid = ?;";
    $query_all = $db->prepare($sql);
    $key_tmp = filter_var($itemID, FILTER_SANITIZE_NUMBER_INT);
    $query_all->bindParam(1, $key_tmp, PDO::PARAM_INT);
    $query_all->execute();
    $res = $query_all->fetch();
    $prod_name = $res["NAME"];
    $prod_price = $res["PRICE"];
    $responseStr = "$prod_name~$prod_price";

    echo $responseStr;
    exit();
}
?>