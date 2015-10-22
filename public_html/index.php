<!DOCTYPE html>
<!-- account.php -->
<html lang="en">

<head>
<meta charset="utf-8" />
<title>dnatabase</title>
</head>

<body>
<h1>Welcome to GenIUS</h1>


<form action="search_query.php" method="post">
<p>
Search Gene:
<input type="text" name="gene_name" />
</p>
<br />

<div id="wrapper">
        <div id="banner">
        </div>

        <nav id="navigation">
            <ul id="nav">
                <li><a href="login_email.php">Log In</a></li>
                <li><a href="account.php">Sign Up</a></li>
            </ul>
        </nav>
            
        <div id="content_area">
            <?php echo $content; ?>
        </div>
            
        <div id="sidebar">
        </div>
</div>
<?php
include './Template.php';
?>
</body>

</html>
