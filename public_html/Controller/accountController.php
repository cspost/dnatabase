<!DOCTYPE html>
<!-- accountController.php -->

<script>
//Display a confirmation box when trying to delete an object
function showConfirm(id)
{
    // build the confirmation box
    var c = confirm("Are you sure you wish to delete this item?");
    
    // if true, delete item and refresh
    if(c)
        window.location = "CoffeeOverview.php?delete=" + id;
}
</script>

<?php
include_once("Entity/userEntity.php");

//Contains non-database related function for the Coffee page
class accountController {
    public $username="dnatabas_root";
    public $password="alphabio";
    public $database="dnatabas_cs411";
    public $host = "dnatabase.web.engr.illinois.edu";
    
    function CreateOverviewTable() {
        $result = "
            <table class='overViewTable'>
                <tr>
                    <td></td>
                    <td></td>
                    <td><b>Id</b></td>
                    <td><b>Name</b></td>
                    <td><b>Type</b></td>
                    <td><b>Price</b></td>
                    <td><b>Roast</b></td>
                    <td><b>Country</b></td>
                </tr>";

        $coffeeArray = $this->GetCoffeeByType('%');

        foreach ($coffeeArray as $key => $value) {
            $result = $result .
                    "<tr>
                        <td><a href='CoffeeAdd.php?update=$value->id'>Update</a></td>
                        <td><a href='#' onclick='showConfirm($value->id)'>Delete</a></td>
                        <td>$value->id</td>
                        <td>$value->name</td>
                        <td>$value->type</td>    
                        <td>$value->price</td> 
                        <td>$value->roast</td>
                        <td>$value->country</td>   
                    </tr>";
        }

        $result = $result . "</table>";
        return $result;
    }

    function CreateCoffeeDropdownList() {
        $coffeeModel = new CoffeeModel();
        $result = "<form action = '' method = 'post' width = '200px'>
                    Please select a type: 
                    <select name = 'types' >
                        <option value = '%' >All</option>
                        " . $this->CreateOptionValues($this->GetCoffeeTypes()) .
                "</select>
                     <input type = 'submit' value = 'Search' />
                    </form>";

        return $result;
    }

    function showUserInfo($email) {
        $result = "";

        foreach ($valueArray as $value) {
            $result = $result . "<option value='$value'>$value</option>";
        }

        return $result;
    }

    function createUserTables($email) {
        $result = "";
        $mysqli = new mysqli($this->host,$this->username,$this->password,$this->database);
        $check = $mysqli->query("SELECT * FROM user WHERE
        email='$email'");

        if ($user_info = $mysqli->query("SELECT * FROM user WHERE email='$email'"))
        {
            while ($out = $user_info->fetch_assoc())
            {
                $first_name = $out['firstname'];
                $last_name = $out['lastname'];
                $institution = $out['institution'];
                
                $result .= "<table class = 'coffeeTable'>
                <tr>
                    <th>First Name: </th>
                    <td>$first_name</td>
                </tr>

                <tr>
                    <th>Last Name: </th>
                    <td>$last_name</td>
                </tr>

                <tr>
                    <th> Institution:</th>
                    <td>$institution</td>
                </tr>

                <tr>
                    <th>User Email:</th>
                    <td>$email</td>
                </tr>

                <tr>
                <th>========</th>
                </tr>
                <tr>
                    <th>Gene List: </th>
                </tr>
                <tr>
                    <th>symbol</th>
                    <th>name</th>¬
                    <th>species</th>
                    <th>url</th>
                </tr>
                </table>";

                if ($query_research = $mysqli->query("SELECT symbol FROM researches WHERE email = '$email'"))
                {
                    
                    while ($research_row = $query_research->fetch_assoc())
                    {
                        $symbol = $research_row['symbol'];
                        if ($query_symbol = $mysqli->query("SELECT * FROM genesymbol WHERE symbol='$symbol'")){
                            while ($row = $query_symbol->fetch_assoc())
                            {
                        
                                $name = $row['name'];
                                $species = $row['species'];
                                $url = $row['url'];
                                $result .= 
                                "<table class = 'coffeeTable'>
                                <tr>
                                    <td>$symbol</td>
                                    <td>$name</td>
                                    <td>$species</td>
                                    <td><a href='$url'>$url<></td>
                                </tr>
                                </table>";
                            }
                        }
                    }
                }
            }
        }
        if (!$result){
            $result .= "User not found!\n";
        }
        $mysqli->close();
        return $result;
    }

    //Returns list of files in a folder.
    function getGenes($gene_list) {
        print("Gettning genes...\n");
        $symbol = array();
        $mysqli = new mysqli($this->host, $this->username,$this->password,$this->database);
        foreach($gene_list as $gene){
            print("$gene\n");
            $query = "SELECT symbol FROM gene WHERE gid='$gene'";
            if ($result = $mysqli->query($query)){
                while ($row = $result->fetch_assoc()){
                    array_push($symbol,$row);
                    print("within while loop\n");
                }
            }
        }
        return $symbol;
        $mysqli->close();
    }
    
    function queryGeneSymbol($gene_name) {
        $result = "";
        $mysqli = new mysqli($this->host,$this->username,$this->password,$this->database);
        if ($query_symbol = $mysqli->query("SELECT * FROM genesymbol WHERE symbol='$gene_name'"))
        {
            while ($row = $query_symbol->fetch_assoc())
            {
                $name = $row['name'];
                $species = $row['species'];
                $url = $row['url'];
                $result .= "<table class = 'coffeeTable'>
                                <tr>
                                    <td>symbol</td>
                                    <td>name</td>
                                    <td>species</td>
                                    <td>url</td>
                                </tr>
                                <tr>
                                    <td>$gene_name</td>
                                    <td>$name</td>
                                    <td>$species</td>
                                    <td><a href='$url'>$url<></td>
                                </tr>
                                </table>";
            }
        }
        if (!$result){
            $result .= "Gene not found!\n";
        }
        return $result;
    }

    function PerformQuery($query) {
        $mysqli = new mysqli($this->host, $this->username, $this->password,$this->database);
        if ($mysqli->connect_errno) {
            die("Connect failed: " . $mysqli->connect_errno);
        }
        $result = $mysqli->query($query);
        if (!$result){
            printf("Error: %s\n", $mysqli->error);
        }
        else{
            echo "Query Runs!" . '<br>';
        }
        $mysqli->close();
    }
 
    function addNewUser($email,$first_name,$institution,$last_name,$gene_symbol) {
        $mysqli = new mysqli($this->host, $this->username,$this->password,$this->database);
        $check = $mysqli->query("SELECT * FROM user WHERE email = '$email'");
        if ($check->fetch_assoc() != false){
            //$this->addNewSearchers($email, $gene_symbol);
            return("Welcome back $first_name!\n");
        }
        else{
            $query = "INSERT INTO user
                          (email, firstname, institution, lastname)
                          VALUES ('$email','$first_name','$institution','$last_name')";
            $result = $mysqli->query($query);
            $this->addNewSearchers($email, $gene_symbol);
        }
        $mysqli->close();
        return("$first_name, we have registered your account in our database!\n");
    }

    function addNewSearchers($email, $gene_symbol){
        $mysqli = new mysqli($this->host, $this->username,$this->password,$this->database);
        foreach($gene_symbol as $symbol){
            $query = "INSERT INTO researches
                            (email, symbol)
                            VALUES ('$email','$symbol')";
            $res = $mysqli->query($query);
            if ($res){
                print("insert $symbol\n");
            }
        }
        $mysqli->close();
    }

    function UpdateProfile($old_email, $new_email,$first_name,$institution,$last_name,$gene_symbol) {
        $mysqli = new mysqli($this->host,$this->username,$this->password,$this->database);
        $update = $mysqli->query("UPDATE user SET email = '$new_email',
        firstname='$first_name', institution='$institution',
        lastname='$last_name' WHERE email = '$old_email'");
        
        $delete_researcher = $mysqli->query("DELETE FROM researches WHERE email =
        '$new_email'");

#        printf("Affected rows (delete): %d, user: %s\n",$delete_old_researcher->affected_rows, $old_email);
        #$update_researcher = $mysqli->query("UPDATE researches SET email = '$email' WHERE email = '$email'");
        #$mysqli->query($update);
        foreach($gene_symbol as $symbol){
            #$check = $mysqli->query("SELECT * FROM researches WHERE
            #email='$email' AND symbol='$symbol'");
            #if ($check->fetch_assoc() == false){
                if ($symbol == "") next;
                $mysqli->query("INSERT INTO researches
                                    (email, symbol)
                                    VALUES ('$new_email','$symbol')");
            #}
        }
        $mysqli->close();
        $message = "Your profile has been updated!\n";
        return $message;
    }

    function DeleteAccount($email) 
    {
        $mysqli = new mysqli($this->host,$this->username,$this->password,$this->database);
        $delete_user = $mysqli->query("DELETE FROM user WHERE email = '$email'");
        $delete_researcher = $mysqli->query("DELETE FROM researches WHERE email =
        '$email'");
        if ($delete_user && $delete_researcher){
            return("Profile associated with $email has been deleted!\n");    
        }
    }

    //</editor-fold>
    //<editor-fold desc="Get Methods">
    function GetCoffeeById($id) {
        $coffeeModel = new CoffeeModel();
        return $coffeeModel->GetCoffeeById($id);
    }

    function GetCoffeeByType($type) {
        $coffeeModel = new CoffeeModel();
        return $coffeeModel->GetCoffeeByType($type);
    }

    function GetCoffeeTypes() {
        $coffeeModel = new CoffeeModel();
        return $coffeeModel->GetCoffeeTypes();
    }

    //</editor-fold>
}
?>

