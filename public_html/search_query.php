<!DOCTYPE html>
<!-- search_query.php -->
<html lang="en">
<head>
<meta charset="utf-8" />
<title>Search for gene</title>
</head>

<?php
include_once("Entity/userEntity.php");
include_once("Controller/accountController.php");

$accountController = new accountController();
$content = "";
$content = $accountController->queryGeneSymbol($_POST["gene_name"]);

include 'Template.php';
?>

</body>

</html>
