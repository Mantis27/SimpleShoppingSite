<?php
session_start();
include_once('lib/auth.php');
include_once('lib/nonce.php');
if (!authAdmin()) {
    header('Location: login.php', true, 302);
}

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
    <fieldset id="field_prod_insert">
        <legend>Product Insert</legend>
        <form id="prod_insert" method="POST" action="admin-process.php?action=<?php echo ($action = 'prod_insert');?>" enctype="multipart/form-data">
            <label for="prod_catid"> Category *</label>
            <div> <select id="prod_catid" name="catid"><?php echo $options; ?></select></div>
            <label for="prod_name"> Name *</label>
            <div> <input id="prod_name" type="text" name="name" required="required" pattern="^[\w\- ]+$"/></div>
            <label for="prod_price"> Price *</label>
            <div> <input id="prod_price" type="text" name="price" required="required" pattern="^\d+\.?\d*$"/></div>
            <label for="prod_desc"> Description *</label>
            <div> <textarea id="prod_desc" type="text" name="description"></textarea> </div>
            <label for="prod_stock"> Inventory *</label>
            <div> <input id="prod_stock" type="number" name="stock" required="required" min="1"/> </div>
            <label for="prod_image_insert"> Image (Press the button, OR Drag-and-Drop your file here) *</label>
            <div> <input id="prod_image_insert" type="file" name="file1" required="true" accept="image/jpeg" onchange="handleFiles(this)" onclick="this.value=null;"/> </div>
            <div> <img id="target_insert"/> </div> 
            <input type="submit" value="Submit"/>
            <input type="hidden" name="nonce" value="<?php echo csrf_getNonce($action); ?>"/>
        </form>
    </fieldset>
    <fieldset id="field_prod_edit">
        <legend>Product Edit</legend>
        <form id="prod_edit" method="POST" action="admin-process.php?action=<?php echo ($action = 'prod_edit');?>" enctype="multipart/form-data">
            <label for="prod_id"> Product ID *</label>
            <div> <select id="prod_id" name="pid"><?php echo $prod_options; ?></select></div>
            <label for="prod_catid"> Category *</label>
            <div> <select id="prod_catid" name="catid"><?php echo $options; ?></select></div>
            <label for="prod_name"> Name *</label>
            <div> <input id="prod_name" type="text" name="name" required="required" pattern="^[\w\- ]+$"/></div>
            <label for="prod_price"> Price *</label>
            <div> <input id="prod_price" type="text" name="price" required="required" pattern="^\d+\.?\d*$"/></div>
            <label for="prod_desc"> Description *</label>
            <div> <textarea id="prod_desc" type="text" name="description"></textarea> </div>
            <label for="prod_stock"> Inventory *</label>
            <div> <input id="prod_stock" type="number" name="stock" required="required" min="1"/> </div>
            <label for="prod_image_edit"> Image (Press the button, OR Drag-and-Drop your file here) * </label>
            <div> <input id="prod_image_edit" type="file" name="file" required="true" accept="image/jpeg" onchange="handleFiles(this)" onclick="this.value=null;"/> </div>
            <div> <img id="target_edit"/> </div> 
            <input type="submit" value="Submit"/>
            <input type="hidden" name="nonce" value="<?php echo csrf_getNonce($action); ?>"/>
        </form>
    </fieldset>
    <fieldset>
        <legend>Product Delete</legend>
        <form id="prod_delete_by_catid" method="POST" action="admin-process.php?action=<?php echo ($action = 'prod_delete_by_catid');?>" enctype="multipart/form-data">
            <label for="prod_id"> Product ID *</label>
            <div> <select id="prod_id" name="pid"><?php echo $prod_options; ?></select></div>
            <input type="submit" value="Submit"/>
            <input type="hidden" name="nonce" value="<?php echo csrf_getNonce($action); ?>"/>
        </form>
    </fieldset>
    <fieldset>
        <legend>Category Insert</legend>
        <form id="cat_insert" method="POST" action="admin-process.php?action=<?php echo ($action = 'cat_insert');?>" enctype="multipart/form-data">
            <label for="cat_name"> Cat Name *</label>
            <div> <input id="cat_name" type="text" name="name" required="required" pattern="^[\w\-]+$"/> </div>
            <input type="submit" value="Submit"/>
            <input type="hidden" name="nonce" value="<?php echo csrf_getNonce($action); ?>"/>
        </form>
    </fieldset>
    <fieldset>
        <legend>Category Edit</legend>
        <form id="cat_edit" method="POST" action="admin-process.php?action=<?php echo ($action = 'cat_edit');?>" enctype="multipart/form-data">
            <label for="cat_id"> Category *</label>
            <div> <select id="cat_id" name="catid"><?php echo $options; ?></select></div>
            <label for="cat_name"> New Name *</label>
            <div> <input id="cat_name" type="text" name="name" required="required" pattern="^[\w\-]+$"/> </div>
            <input type="submit" value="Submit"/>
            <input type="hidden" name="nonce" value="<?php echo csrf_getNonce($action); ?>"/>
        </form>
    </fieldset>

    <fieldset>
        <legend>Category Delete</legend>
        <form id="cat_delete" method="POST" action="admin-process.php?action=<?php echo ($action = 'cat_delete');?>" enctype="multipart/form-data">
            <label for="cat_id"> Category *</label>
            <div> <select id="cat_id" name="catid"><?php echo $options; ?></select></div>
            <input type="submit" value="Submit"/>
            <input type="hidden" name="nonce" value="<?php echo csrf_getNonce($action); ?>"/>
        </form>
    </fieldset>
    <a href="/index.php">Back to Shop</a>
    <script src="/lib/admin-script.js"></script>
</html>
