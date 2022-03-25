<!DOCTYPE html>
<?php session_start(); ?>
<html>
    <head>
        <!-- Bootstrap framework -->
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link rel="stylesheet" href="style_item.css" >
        <title>IERG4210 Project</title>
        <?php 
            $db = new PDO('sqlite:/var/www/cart.db'); 
            $prod_pid = $_REQUEST["item"];
            $prod_pid_tmp = filter_var($prod_pid, FILTER_SANITIZE_NUMBER_INT);
            $sql = "SELECT catid, name, price, description, stock FROM products WHERE pid = ?;";
            $query_all = $db->prepare($sql);
            $query_all->bindParam(1, $prod_pid_tmp, PDO::PARAM_INT);
            if ($query_all->execute()) {
                $head_res = $query_all->fetch();
                $prod_catid = $head_res["CATID"];
                $prod_name = $head_res["NAME"];
                $prod_price = $head_res["PRICE"];
                $prod_desc = $head_res["DESCRIPTION"];
                $prod_stock = $head_res["STOCK"];
            }
            include_once('../lib/auth.php');
            $auth_email = auth();
            if (!$auth_email) {
                // false, fake/no cookie
                $auth_email = "GUEST";
            }
        ?>
    </head>
    <body>
        <div class="container">
            <nav class="navbar navbar-light bg-light">
                <a class="navbar-brand" href="#">Andy's Simple Shopping Mall</a>
                <p class="navbar-text"><a href="/admin.php">Admin Page</a></p>
                <p class="navbar-text">Login-ed as: <?php echo htmlspecialchars($auth_email); ?></p>
                <?php
                if ($auth_email == "GUEST") {
                    echo '<p class="navbar-text"><a href="/login.php">LogIn</a></p>';
                }
                else {
                    echo '<p class="navbar-text"><a href="/changepw.php">Change PW</a></p>';
                    echo '<p class="navbar-text"><a href="/logout.php">LogOut</a></p>';
                }
                ?>
            </nav>
            <div class="row">
                <section id="left" class="col-2">
                    <div class="shoppinglist">       
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
                                    <li><a href="/Catagory/index.php?cat=<?php echo urlencode($catid); ?>"><?php echo htmlspecialchars($name); ?></a></li>
                                    <?php
                                }
                            ?>
                        </ul>
                    </div>
                </section>
                <section id="right" class="col-10">
                    <div class="linklist">
                        <p class="linktext">
                            <a href="/index.php">Main</a> > <a href="/Catagory/index.php?cat=<?php echo urlencode($prod_catid) ?>">
                            <?php 
                                    $sql="SELECT name FROM categories WHERE catid = ?;";
                                    $q = $db->prepare($sql);
                                    $currentCat_tmp = filter_var($prod_catid, FILTER_SANITIZE_NUMBER_INT);
                                    $q->bindParam(1, $currentCat_tmp, PDO::PARAM_INT);
                                    if ($q->execute()) {
                                        $head_res = $q->fetch();
                                        echo htmlspecialchars($head_res["NAME"]);
                                    }
                                ?>
                            </a> > <a href="#"><?php echo $prod_name; ?></a>
                        </p>       
                    </div>

                    <div class="itemdetail">
                        <img src="/Resources/Item_Photo/<?php echo htmlspecialchars($prod_pid); ?>.jpg" class="largepic"/>
                        <h3><?php echo htmlspecialchars($prod_name); ?></h3>
                        <p>Price: $<?php echo htmlspecialchars($prod_price); ?></p>
                        <p>Stock: <?php echo htmlspecialchars($prod_stock); ?></p>
                        <button onclick="add_to_cart(<?php echo htmlspecialchars($prod_pid); ?>)">Add to Cart</button>
                        <hr>
                        <p><?php echo $prod_desc; ?></p>
                    </div>
                </section>
            </div>
        </div>
        <script src="/lib/add_prod.js"></script>
    </body>
</html>
