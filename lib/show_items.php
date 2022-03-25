<?php
$db = new PDO('sqlite:/var/www/cart.db');

if (isset($_POST["query_page"])) {
    $reuslt = "";
    $newPage = $_POST["query_page"];
    $perPage = $_POST["per_page"];
    $category = $_POST["category"];

    $sql = "SELECT pid, name, price FROM products WHERE catid = ? LIMIT ?, ?;";
    $query_all = $db->prepare($sql);

    $sql_p_category = filter_var($category, FILTER_SANITIZE_NUMBER_INT);
    $query_all->bindParam(1, $sql_p_category, PDO::PARAM_INT);

    $sql_p_from = (filter_var($newPage, FILTER_SANITIZE_NUMBER_INT) - 1) * filter_var($perPage, FILTER_SANITIZE_NUMBER_INT);
    $query_all->bindParam(2, $sql_p_from, PDO::PARAM_INT);

    $sql_p_max = filter_var($perPage, FILTER_SANITIZE_NUMBER_INT);
    $query_all->bindParam(3, $sql_p_max, PDO::PARAM_INT);

    if ($query_all->execute()) {
        $prod_res = $query_all->fetchAll();
        foreach($prod_res as $prod_element) {
            $pid = $prod_element["PID"];
            $prod_name = $prod_element["NAME"];
            $prod_price = $prod_element["PRICE"];
            $result .= "<li><a href='/Items/index.php?item=";
            $result .= urlencode($pid);
            $result .= "'><img src='/Resources/Item_Photo/";
            $result .= htmlspecialchars($pid);
            $result .= ".jpg'/><br>";
            $result .= htmlspecialchars($prod_name);
            $result .= "</a><br>$";
            $result .= htmlspecialchars($prod_price);
            $result .= "<button onclick='add_to_cart(";
            $result .= htmlspecialchars($pid);
            $result .= ")'>Add</button></li>";

        }                                    
    }
    echo $result;
    exit();
}
?>