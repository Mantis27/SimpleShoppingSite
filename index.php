<!DOCTYPE html>
<html>
    <head>
        <!-- Bootstrap framework -->
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link rel="stylesheet" href="style1.css" >
        <title>IERG4210 Project</title>
        <?php $db = new PDO('sqlite:/var/www/cart.db'); ?>
    </head>
    <body>
        <div class="container">
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
                                    <li><a href="/Catagory/index.php?cat=<?php echo htmlspecialchars($catid); ?>"><?php echo $name; ?></a></li>
                                    <?php
                                }
                            ?>
                        </ul>
                    </div>
                </section>
                <section id="right" class="col-10">
                    <div class="linklist">
                        <p class="linktext">
                            <a href="#">Main</a>
                        </p>       
                    </div>
                    <h2>Welcome to the store.</h2>
                    <p>Please select a Catagory from the left.</p>
                    <p>Currently only Catagory1 implemented, further catagories can be done in the same fashion.</p>
                </section>
            </div>
        </div>
        <script src="/lib/add_prod.js"></script>
    </body>
</html>
