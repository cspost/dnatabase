<!DOCTYPE html>
<!-- create_account.php -->
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
if (isset($_POST["email"]) && isset($_POST["first_name"]) && isset($_POST["last_name"]) && isset($_POST["institution"]) && isset($_POST["gene_list"])){
    $gene_list = preg_split('/\s+/', $_POST['gene_list']);

    $user = new userEntity($_POST["first_name"], $_POST["last_name"],$_POST["email"], $_POST["institution"], $gene_list, $gene_list );

    $content = $accountController->addNewUser($user->email,
    $user->first_name,$user->institution, $user->last_name, $user->gene_symbol);
//    $user->gene_symbol = $accountController->getGenes($user->gene_list);
//    $accountController->addNewSearchers($user->gene_symbol);
    $content .= $accountController->createUserTables($user->email);
}
include_once("Template.php");
?>


</body>
</html>
