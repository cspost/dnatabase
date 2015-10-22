<!DOCTYPE HTML> 
<html>
<head>
<style>
.error {color: #FF0000;}
</style>
</head>
<body> 

<?php
// define variables and set to empty values
$firstnameErr = $lastnameErr = $oldemailErr = $newemailErr = $institutionErr ="";
$firstname = $lastname = $oldemail = $newemail = $institution =  "";

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{
   if (empty($_POST["first_name"])) {
     $firstnameErr = "First Name is required";
   } else {
     $firstname = test_input($_POST["first_name"]);
     // check if name only contains letters and whitespace
     if (!preg_match("/^[a-zA-Z ]*$/",$firstname)) {
       $firstnameErr = "Only letters and white space allowed"; 
     }
   }
   
    if (empty($_POST["last_name"])) {
        $lastnameErr = "Last Name is required";
    } 
    else {
        $lastname = test_input($_POST["last_name"]);
     // check if name only contains letters and whitespace
        if (!preg_match("/^[a-zA-Z ]*$/",$lastname)) {
        $lastnameErr = "Only letters and white space allowed"; 
        }
   }

   if (empty($_POST["old_email"])) {
     $oldemailErr = "Email is required";
   } else {
     $oldemail = test_input($_POST["old_email"]);
     // check if e-mail address is well-formed
     if (!filter_var($oldemail, FILTER_VALIDATE_EMAIL)) {
       $oldemailErr = "Invalid email format"; 
     }
   }

    
   if (empty($_POST["new_email"])) {
     $newemailErr = "Email is required";
   } else {
     $newemail = test_input($_POST["new_email"]);
     // check if e-mail address is well-formed
     if (!filter_var($newemail, FILTER_VALIDATE_EMAIL)) {
       $newemailErr = "Invalid email format"; 
     }
   }
     
   if (empty($_POST["institution"])) {
     $institutionErr = "Institution is required";
   } else {
     $instutition = test_input($_POST["institution"]);
   }
}

function test_input($data) {
   $data = trim($data);
   $data = stripslashes($data);
   $data = htmlspecialchars($data);
   return $data;
}
?>

<form action="update_profile.php" method="post">
    <fieldset>
        <legend>Change My Profile</legend>
        <p><span class="error">* required field.</span></p>
        <label for="first_name">First Name: </label>
        <input type='text' class='inputField' name='first_name' value="<?php echo $firstname;?>" />
        <span class="error">* <?php echo $firstnameErr;?></span>
        <br/>

        <label for='last_name'>Last Name: </label>
        <input type='text' class='inputField' name='last_name' value="<?php echo
        $lastname;?>"/>
        <span class="error">* <?php echo $lastnameErr;?></span>
        <br/>

        <label for='old_email'>Old Email: </label>
        <input type='text' class='inputField' name='old_email' value="<?php echo
        $email;?>" />
        <span class="error">* <?php echo $emailErr;?></span>
        <br/>
        
        <label for='new_email'>New Email: </label>
        <input type='text' class='inputField' name='new_email' value="<?php echo
        $email;?>" />
        <span class="error">* <?php echo $emailErr;?></span>
        <br/>

        <label for='institution'>Institution: </label>
        <input type='text' class='inputField' name='institution' value="<?php
        echo $institution;?>" />
        <span class="error">* <?php echo $institutionErr;?></span>
        <br/>

        <label for='gene_list'>Gene List: </label>
        <textarea cols='70' rows='12' name='gene_list'></textarea></br>

        <input type='submit' value='Submit'>
    </fieldset>
</form>


</body>
</html>

<?php
$title = "Change my profile";
include_once("./Template.php");
?>
