<!DOCTYPE html>
<html>
    <head>
        <!-- Bootstrap framework -->
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link rel="stylesheet" href="style_cat.css">
        <title>IERG4210 Project</title>
        <?php 
            $db = new PDO('sqlite:/var/www/cart.db'); 
            $currentCat = $_REQUEST["cat"];
        ?>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <section id="left" class="col-2">
                    <div class="shoppinglist">
                        <p class="shorttext">Shopping List $10</p>
                        <ul class="longtext list-group list-unstyled">
                            <li>Shopping List $10</li>
                            <li>Item1 <input type="text" size="2" value="1"/> @2</li>
                            <li>Item2 <input type="text" size="2" value="1"/> @8</li>
                            <li><button class="checkout">Checkout</button></li>
                        </ul>
                    </div>
                    <div class="menu">
                        <p>Catagories</p>
                        <ul class="list-group list-unstyled">
                            <?php
                                $query_all = $db->query("SELECT catid, name FROM categories;");
                                $cat_res = $query_all->fetchAll();
                                foreach($cat_res as $cat_element) {
                                    $catid = $cat_element["CATID"];
                                    $name = $cat_element["NAME"];
                                    ?>
                                    <li><a href="/Catagory/index.php?cat=<?php echo $catid; ?>"><?php echo $name; ?></a></li>
                                    <?php
                                }
                            ?>
                        </ul>
                    </div>
                </section>
                <section id="right" class="col-10">
                    <div class="linklist">
                        <p class="linktext">
                            <a href="/index.php">Main</a> > <a href="#">
                                <?php 
                                    $query_all = $db->query("SELECT name FROM categories WHERE catid = $currentCat;");
                                    $head_res = $query_all->fetch();
                                    echo $head_res["NAME"];
                                ?>
                            </a>
                        </p>       
                    </div>

                    <div class="itemlist">
                        <ul class="itemtable">
                            <?php
                                $query_all = $db->query("SELECT pid, name, price FROM products WHERE catid = $currentCat;");
                                $prod_res = $query_all->fetchAll();
                                foreach($prod_res as $prod_element) {
                                    $pid = $prod_element["PID"];
                                    $prod_name = $prod_element["NAME"];
                                    $prod_price = $prod_element["PRICE"];
                                    ?>
                                    <li><a href="/Items/index.php"><img src="/Resources/Item_Photo/<?php echo $pid; ?>.jpg"/><br><?php echo $prod_name; ?></a><br>$<?php echo $prod_price; ?> <button>Add</button></li>
                                    <?php
                                }
                            ?>
                        </ul>
                    </div>
                </section>
    
            </div>
    
        </div>
    </body>
</html>
