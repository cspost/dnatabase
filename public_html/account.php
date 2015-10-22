<form action="create_account.php" method="post">
    <fieldset>
        <legend>Create a new Account</legend>

        <label for="first_name">First Name: </label>
        <input type='text' class='inputField' name='first_name' /><br/>

        <label for='last_name'>Last Name: </label>
        <input type='text' class='inputField' name='last_name' /><br/>

        <label for='email'>Email: </label>
        <input type='text' class='inputField' name='email' /><br/>

        <label for='institution'>Institution: </label>
        <input type='text' class='inputField' name='institution' /><br/>

        <label for='gene_list'>Gene List: </label>
        <textarea cols='70' rows='12' name='gene_list'></textarea></br>

        <input type='submit' value='Submit'>
    </fieldset>
</form>

<?php
$title = "Create a new Account";
include_once("./Template.php");
?>
