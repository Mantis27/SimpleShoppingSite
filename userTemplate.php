<!DOCTYPE html>
<html>
<body>

<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
  PW: <input type="text" name="fpw">
  <input type="submit">
</form>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // collect value of input field
    $name = htmlspecialchars($_REQUEST['fpw']);
    if (empty($name)) {
        echo "PW is empty";
    } else {
        $salt = mt_rand();
        echo "Salt: ".$salt."<br>";
        echo "Salted PW: ".hash_hmac('sha256', $_REQUEST['fpw'], $salt);
    }
}
?>

</body>
</html>