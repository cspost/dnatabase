<!DOCTYPE html>
<!-- deleteAccount.php -->
<html lang="en">
<head>
<meta charset="utf-8" />
<title>Delete account</title>
</head>

<?php
include_once("Entity/userEntity.php");
include_once("Controller/accountController.php");

$accountController = new accountController();
$content = "";
$content = $accountController->DeleteAccount($_POST["email"]);

include './Template.php';
?>

</body>

</html>
