<?php
require __DIR__.'/lib/db.inc.php';
$res = ierg4210_cat_fetchall();
$options = '';

foreach ($res as $value){
    $options .= '<option value="'.$value["CATID"].'"> '.$value["NAME"].' </option>';
}

$prod_res = ierg4210_prod_fetchAll();
$prod_options = '';
foreach ($prod_res as $prod_value){
    $prod_options .= '<option value="'.$prod_value["PID"].'"> '.$prod_value["NAME"].' </option>';
}

?>

<html>
    <h2>Admin Page</h2>
    <fieldset>
        <legend>Product Insert</legend>
        <form id="prod_insert" method="POST" action="admin-process.php?action=prod_insert" enctype="multipart/form-data">
            <label for="prod_catid"> Category *</label>
            <div> <select id="prod_catid" name="catid"><?php echo $options; ?></select></div>
            <label for="prod_name"> Name *</label>
            <div> <input id="prod_name" type="text" name="name" required="required" pattern="^[\w\- ]+$"/></div>
            <label for="prod_price"> Price *</label>
            <div> <input id="prod_price" type="text" name="price" required="required" pattern="^\d+\.?\d*$"/></div>
            <label for="prod_desc"> Description *</label>
            <div> <input id="prod_desc" type="text" name="description"/> </div>
            <label for="prod_stock"> Inventory *</label>
            <div> <input id="prod_stock" type="number" name="stock" required="required" min="1"/> </div>
            <label for="prod_image"> Image * </label>
            <div> <input id="prod_image" type="file" name="file1" required="true" accept="image/jpeg"/> </div>
            <input type="submit" value="Submit"/>
        </form>
    </fieldset>
    <fieldset>
        <legend>Product Edit</legend>
        <form id="prod_edit" method="POST" action="admin-process.php?action=prod_edit" enctype="multipart/form-data">
            <label for="prod_id"> Product ID *</label>
            <div> <select id="prod_id" name="pid"><?php echo $prod_options; ?></select></div>
            <label for="prod_catid"> Category *</label>
            <div> <select id="prod_catid" name="catid"><?php echo $options; ?></select></div>
            <label for="prod_name"> Name *</label>
            <div> <input id="prod_name" type="text" name="name" required="required" pattern="^[\w\- ]+$"/></div>
            <label for="prod_price"> Price *</label>
            <div> <input id="prod_price" type="text" name="price" required="required" pattern="^\d+\.?\d*$"/></div>
            <label for="prod_desc"> Description *</label>
            <div> <input id="prod_desc" type="text" name="description"/> </div>
            <label for="prod_stock"> Inventory *</label>
            <div> <input id="prod_stock" type="number" name="stock" required="required" min="1"/> </div>
            <label for="prod_image"> Image * </label>
            <div> <input type="file" name="file" required="true" accept="image/jpeg"/> </div>
            <input type="submit" value="Submit"/>
        </form>
    </fieldset>
    <fieldset>
        <legend>Delete Product</legend>
        <form id="prod_delete_by_catid" method="POST" action="admin-process.php?action=prod_delete_by_catid" enctype="multipart/form-data">
            <label for="prod_id"> Product ID *</label>
            <div> <select id="prod_id" name="pid"><?php echo $prod_options; ?></select></div>
            <input type="submit" value="Submit"/>
        </form>
    </fieldset>
    <fieldset>
        <legend>New Catagory</legend>
        <form id="cat_insert" method="POST" action="admin-process.php?action=cat_insert" enctype="multipart/form-data">
            <label for="cat_name"> Cat Name *</label>
            <div> <input id="cat_name" type="text" name="name" required="required" pattern="^[\w\-]+$"/> </div>
            <input type="submit" value="Submit"/>
        </form>
    </fieldset>
    <fieldset>
        <legend>Delete Catagory</legend>
        <form id="cat_delete" method="POST" action="admin-process.php?action=cat_delete" enctype="multipart/form-data">
            <label for="cat_id"> Category *</label>
            <div> <select id="cat_id" name="catid"><?php echo $options; ?></select></div>
            <input type="submit" value="Submit"/>
        </form>
    </fieldset>
</html>
