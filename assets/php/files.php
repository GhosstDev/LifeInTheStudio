<?php
@session_start();
require 'aws.php';

$ini = parse_ini_file('../../bin/config.ini');
if ($ini['debug']) {error_reporting(E_ALL);} else {error_reporting(0);}
set_include_path($ini['include_path']);

$ser = new mysqli($ini['host'], $ini['username'], $ini['password'], $ini['database']);
if ($ser->connect_error) {
    echo "err_mysql_server_connection";

} else {
    if (isset($_FILES)) {
        foreach ($_FILES as $key => $file) {
            
        }
    }
    if (isset($_GET['action'])) {
        $action = $_GET['action'];
        
    } else {
        echo "err_no_action";
    }    
}

function checkFields($required_fields) {
    if (!empty($_POST)) {
        $missing_fields = [];
        foreach ($required_fields as $val) {
            if (!isset($_POST[$val])) {
                array_push($missing_fields, $val);
            }
        }
        if (count($missing_fields)) {
            echo "err_post_fields_missing";
            print_r($_POST);
            trigger_error("POST Fields Missing: ".implode(", ", $missing_fields));
            return false;
        } else {
            return true;
        }
    } else {
        echo "err_empty_post";
        return false;
    }
}
