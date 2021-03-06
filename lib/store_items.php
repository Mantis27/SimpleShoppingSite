<?php 
$db = new PDO('sqlite:/var/www/cart.db');

if (isset($_POST["cart_storage"])) {
    $storageStr = $_POST["cart_storage"];
    $storage = json_decode($storageStr);
    
    $totalSum = 0;

    $responseStr = "";
    $responseStr .= "<ul class='longtext list-group list-unstyled' id='longlist_text'>";
    $responseStr .= "<li>Shopping List</li>";

    foreach($storage as $key => $value) {
        if ($key == "Total") {
            continue;
        }
        else {
            // is a pid
            $sql = "SELECT name, price FROM products WHERE pid = ?;";
            $query_all = $db->prepare($sql);
            $key_tmp = filter_var($key, FILTER_SANITIZE_NUMBER_INT);
            $query_all->bindParam(1, $key_tmp, PDO::PARAM_INT);
            $query_all->execute();
            $res = $query_all->fetch();
            $prod_name = $res["NAME"];
            $prod_price = $res["PRICE"];
            $prod_itemPrice = $prod_price * $value;
            $responseStr .= "<li>$prod_name <input id='input_prod_$key' type='text' size='2' value='$value' onchange='input_prod_change($key)'/><button onclick='btn_plus_prod($key)'>+</button><button onclick='btn_minus_prod($key)'>-</button> @$prod_itemPrice</li>";
            $totalSum += $prod_itemPrice;
        }
    }
    $responseStr .= "<li>TOTAL: $$totalSum</li>";
    $responseStr .= "<li><button class='checkout' onclick='window.location.href=\"/checkoutpage.php\"'>Checkout</button></li>";
    $responseStr .= "</ul>";
    $responseStr .= "<p class='shorttext'>Shopping List $$totalSum</p>";
    echo $responseStr;
    exit();
}
?>