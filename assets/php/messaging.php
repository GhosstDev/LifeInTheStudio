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
    if (isset($_GET['action'])) {
        $action = $_GET['action'];
        
        if ($action == "sendMessage") {
            if (checkFields(['thread-id', 'message'])) {
                $thread_info = checkforthread($_POST['thread-id'], $ser);
                if ($thread_info !== false) {
                    if (isset($_SESSION['uuid'])) {
                        $is_participant = false;
                        foreach (json_decode($thread_info['particpants']) as $value) {
                            if ($value == $_SESSION['uuid']) {
                                $is_participant = true;
                            }
                        }
                        $thread_id = $thread_info['thread-id'];
                        $messages_query = $ser->query("SELECT * FROM `t-$thread_id` WHERE `m-index`>=$message_start_index LIMIT=20;");
                        if (is_bool($messages_query) && !$messages_query) {
                            $return_data = [];
                            for ($m=0; $m < $messages_query->num_rows; $m++) { 
                                $message_data = $messages_query->fetch_assoc();
                                array_push($return_data, $message_data);
                            }
                            echo json_encode($message_data);
                        } else {
                            echo "err_sql_query_failed";
                            trigger_error("SQL Query Failed: <br><br>".$ser->error."<br><br>");
                        }
                    } else {
                        echo "err_user_not_authorized";
                    }
                } else {
                    echo "err_invalid_message_thread";
                }
            }



        } elseif ($action == "getMessages") {
            if (checkFields(['thread-id', 'index'])) {
                $thread_info = checkforthread($_POST['thread-id'], $ser);
                if ($thread_info !== false) {
                    if (isset($_SESSION['uuid'])) {
                        $is_participant = false;
                        foreach (json_decode($thread_info['particpants']) as $value) {
                            if ($value == $_SESSION['uuid']) {
                                $is_participant = true;
                            }
                        }
                        $thread_id = $thread_info['thread-id'];
                        $message_start_index = $_POST['index'];
                        $messages_query = $ser->query("SELECT * FROM `t-$thread_id` WHERE `m-index`>=$message_start_index LIMIT=20;");
                        if (is_bool($messages_query) && !$messages_query) {
                            $return_data = [];
                            for ($m=0; $m < $messages_query->num_rows; $m++) { 
                                $message_data = $messages_query->fetch_assoc();
                                array_push($return_data, $message_data);
                            }
                            echo json_encode($message_data);
                        } else {
                            echo "err_sql_query_failed";
                            trigger_error("SQL Query Failed: <br><br>".$ser->error."<br><br>");
                        }
                    } else {
                        echo "err_user_not_authorized";
                    }
                } else {
                    echo "err_invalid_message_thread";
                }

                
            }
        
        } elseif ($action == 'uploadImageAttachment') {
            uploadFileToS3($)
        } else {
            echo "err_bad_action";
        }

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

function checkforthread($thread_id, $mysql_ser_obj) {
    $thread_id_safe = $mysql_ser_obj->real_escape_string($thread_id);
    $thread_search = $mysql_ser_obj->query("SELECT 1 FROM `message-threads` WHERE `thread-id`='$thread_id_safe' LIMIT 1;");
    if (is_bool($thread_search) && !$thread_search) {
        return false;
    } else {
        if ($thread_search->num_rows > 0) {
            return $thread_search->fetch_assoc();
        } else {
            return false;
        }
    }
}