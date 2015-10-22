<!DOCTYPE html>
<!-- userEntity.php -->

<?php
class userEntity
{
    public $first_name;
    public $last_name;
    public $email;
    public $institution;
    public $gene_list;
    public $gene_symbol;
    
    function __construct($first_name, $last_name, $email, $institution,$gene_list, $gene_symbol) {
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->email = $email;
        $this->institution = $institution;
        $this->gene_list = $gene_list;
        $this->gene_symbol = $gene_symbol;
    }
}
?>

