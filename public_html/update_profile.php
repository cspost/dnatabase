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
if (isset($_POST["old_email"]) && isset($_POST["new_email"]) && isset($_POST["first_name"]) && isset($_POST["last_name"]) && isset($_POST["institution"]) && isset($_POST["gene_list"])){
    $gene_list = preg_split('/\s+/', $_POST['gene_list']);
    
    $user = new userEntity($_POST["first_name"],
    $_POST["last_name"],$_POST["new_email"], $_POST["institution"], $gene_list, $gene_list );
    $content = $accountController->UpdateProfile($_POST["old_email"],$_POST["new_email"],$user->first_name,$user->institution, $user->last_name, $user->gene_list);
//    $user->gene_symbol = $accountController->getGenes($user->gene_list);
    $content .= $accountController->createUserTables($_POST["new_email"]);
}
include_once("Template.php");
?>


</body>
</html>
