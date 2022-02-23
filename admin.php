<?php
require __DIR__.'/lib/db.inc.php';
$res = ierg4210_cat_fetchall();
$options = '';

foreach ($res as $value){
    $options .= '<option value="'.$value["CATID"].'"> '.$value["NAME"].' </option>';
}

$actionList = array("prod_insert", "prod_delete_by_catid", "prod_fetchAll", "prod_fetchOne", "prod_edit", "prod_delete");
$actions = '';
foreach ($actionList as $act){
    $actions .= '<option value="'.$act.'"> '.$act.' </option>';
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
            <div> <input id="prod_name" type="text" name="name" required="required" pattern="^[\w\-]+$"/></div>
            <label for="prod_price"> Price *</label>
            <div> <input id="prod_price" type="text" name="price" required="required" pattern="^\d+\.?\d*$"/></div>
            <label for="prod_desc"> Description *</label>
            <div> <input id="prod_desc" type="text" name="description"/> </div>
            <label for="prod_image"> Image * </label>
            <div> <input type="file" name="file" required="true" accept="image/jpeg"/> </div>
            <input type="submit" value="Submit"/>
        </form>
    </fieldset>
    <fieldset>
        <legend>New Catagory</legend>
        <form id="cat_insert" method="POST" action="admin-process.php?action=cat_insert" enctype="multipart/form-data">
            <label for="cat_id"> Cat ID *</label>
            <div> <input id="cat_id" type="number" name="catid" required="required" min="0"/> </div>
            <label for="cat_name"> Cat Name *</label>
            <div> <input id="cat_name" type="text" name="name" required="required" pattern="^[\w\-]+$"/> </div>
            <input type="submit" value="Submit"/>
        </form>
    </fieldset>
</html>
