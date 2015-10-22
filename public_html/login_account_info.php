<!DOCTYPE html>
<!-- deleteAccount.php -->
<html lang="en">
<head>
<meta charset="utf-8" />
<title>My account</title>
</head>
<body>

<ul id="nav">
    <li><a href="changeProfile.php">Change Profile</a></li>
    <li><a href="delete_account_email.php">Delete Account</a></li>
</ul>

<?php
include_once("./Entity/userEntity.php");
include_once("./Controller/accountController.php");

$accountController = new accountController();
$content = "";

$content = $accountController->createUserTables($_POST["email"]);

include 'Template.php';
?>

</body>

</html>
