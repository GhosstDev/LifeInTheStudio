<?php
@session_start();

require 'aws.php';

$ini = parse_ini_file("../../bin/config.ini");
if ($ini['debug']) {error_reporting(E_ALL);} else {error_reporting(0);}
set_include_path($ini['include_path']);

$hash_method = "sha512";
$ser = new mysqli($ini['host'], $ini['username'], $ini['password'], $ini['database']);

if ($ser->connect_errno) {
    echo "err_mysql_server_connection";
} else {
    if (isset($_GET['action'])) {
        $action = $_GET['action'];
        
        if ($action == 'login') {
            if (checkFields(['email','password'])) {
                $email = $ser->real_escape_string($_POST['email']);
                $query1 = $ser->query("SELECT * FROM `accounts` WHERE `email`='$email' LIMIT 1;");
                if ($ser->affected_rows == 1) {
                    $hashed_pass = hash($hash_method, $_POST['password']);
                    $userdata = $query1->fetch_assoc();
                    if ($userdata['password'] == $hashed_pass) {
                        session_reset();
                        $_SESSION['account'] = array(
                            "email" => $userdata['email'],
                            "uuid" => $userdata['uuid']
                        );
                        echo "loggedin";
                    } else {
                        echo "err_invalid_password";
                    }
                } elseif ($ser->affected_rows == -1) {
                    echo "err_sql_query_failed";
                    trigger_error("SQL Query Failed: ".$ser->error);
                } else {
                    echo "err_invalid_username";
                }
            }
        
        
        
        } elseif ($action == "register") {
            if (checkFields(['email','firstName','lastName','password','token','key'])) {
                $token = $ser->real_escape_string($_POST['token']);
                $key = $ser->real_escape_string($_POST['key']);
                $email = $ser->real_escape_string($_POST['email']);
                $firstName = $ser->real_escape_string($_POST['firstName']);
                $lastName = $ser->real_escape_string($_POST['lastName']);
                $token_check = $ser->query("SELECT * FROM `tokens` WHERE `token`='$token' AND `token-key`='$key' AND `type`='signup' AND `status`=true LIMIT 1;");
                if ($token_check->num_rows == 1) {
                    $token_data = $token_check->fetch_assoc();
                    
                    $search = $ser->query("SELECT * FROM `accounts` WHERE `email`='$email' LIMIT 1;");
                    if ($search->num_rows == 0) {
                        $uuid = uuid();
                        $hashed_pass = hash($hash_method, $_POST['password']);
                        $query = $ser->query("INSERT INTO `accounts` (uuid, password, email, name, `current-classes`) VALUES ('$uuid','$hashed_pass','$email','$firstName $lastName','{}')");

                        if ($ser->affected_rows == 1) {
                            $ser->query("UPDATE `tokens` SET `used`=CURRENT_TIMESTAMP AND `status`=false WHERE `token`='$token' AND `token-key`='$key' LIMIT 1;");
                            echo 'success';
                            session_reset();
                            $_SESSION['account'] = array(
                                "email" => $_POST['email'],
                                "uuid" => $uuid
                            );
                        } elseif ($ser->affected_rows == -1) {
                            echo "err_sql_query_failed";
                            trigger_error("SQL Query Failed: ".$ser->error);
                        } else {
                            echo "err_unknown_error";
                            trigger_error("Unknown Error Occurred");
                        }
                    } elseif ($search->num_rows > 0) {
                        echo "err_email_taken";
                    } else {
                        echo "err_sql_query_failed";
                        trigger_error("SQL Query Failed: ".$ser->error);
                    }
                } elseif ($token_check->num_rows == 0) {
                    echo "err_invalid_token";
                    trigger_error("Invalid Signup Token");
                } elseif ($token_check->num_rows == -1) {
                    echo "err_sql_query_failed";
                    trigger_error("SQL Query Failed: ".$ser->error);
                } else {
                    echo "err_unknown_error";
                    trigger("Unknown Error Occurred");
                }
            }
        
        } elseif ($action == "forgotPassword") {
            if (checkFields(['email'])) {
                $email = $ser->real_escape_string($_POST['email']);
                $search = $ser->query("SELECT * FROM `accounts` WHERE `email`='$email' LIMIT 1;");
                if ($search->num_rows > 0) {
                    $account_data = $search->fetch_assoc();
                    $uuid = $account_data['uuid'];
                    $email = $account_data['email'];
                    $email_template = @file_get_contents("/bin/email templates/password-reset-email.html");
                    if ($email_template === false) {
                        echo "err_email_template_failed_to_read";
                        trigger_error(error_get_last()['message']);
                    } else {
                        $token_data = $ser->real_escape_string(json_encode(["uuid" => $uuid ]));
                        $token = hash("sha384", random_bytes(128));
                        $token_key = hash('gost', random_bytes(128));
                        $ser->query("INSERT INTO `tokens` (token, `token-key`, `type`, `token-data`) VALUES ('$token', '$token_key', 'password-reset', '$token_data');");
                        if ($ser->affected_rows == -1) {
                            echo "err_sql_query_failed";
                            trigger_error("SQL Query Failed: ".$ser->err());
                        } else {
                            $link = "https://lifeinthestudio.com/forgotPassword.html?token=$token&key=$token_key";
                            $newEmailBody = str_replace($email_template, "resetPasswordLink", $link);
                            $subject = "Welcome to The Studio!";
                            $params = [
                                'Destination' => ['ToAddresses' => [$_POST['email']]],
                                'ReplyToAddress' => [$ini['replyToEmail']],
                                'Source' => $ini['senderEmail'],
                                'Message' => [
                                    'Body' => [
                                        'Html' => [
                                            'Charset' => $ini['charset'],
                                            'Data' => $newEmailBody
                                        ],
                                        'Text' => [
                                            'Charset' => $ini['charset'],
                                            'Data' => strip_tags($newEmailBody)
                                        ]
                                    ],
                                    'Subject' => [
                                        'Charset' => $ini['charset'],
                                        'Data' => $subject
                                    ]
                                ]
                            ];
                            sendEmail($params);  
                        }
                            
                    }
                } elseif ($search->num_rows == 0) {
                    echo "err_bad_email";
                } elseif ($ser->affected_rows == -1) {
                    echo "err_sql_query_failed";
                    trigger_error("SQL Query Failed: ".$ser->error);
                } else {
                    echo "err_unknown_error";
                    trigger_error("Unknown Error Occurred".$ser->error);
                }
            }


        } elseif ($action == "resetPassword") {
            if (checkFields(['password', 'token', 'key'])) {
                $token_check = $ser->query("SELECT * FROM `tokens` WHERE `token`='$token' AND `token-key`='$key' AND `type`='password-reset' AND `status`=true LIMIT 1;");
                if ($token_check->num_rows == 1) {
                    $token_data = $token_check->fetch_assoc();
                    $hashed_pass = hash($hash_method, $_POST['password']);
                    $uuid = json_decode($token_data['token-data'])->uuid;
                    $ser->query("UPDATE `accounts` SET `password`='$hashed_pass' WHERE `uuid`='$uuid' LIMIT 1;");
                    if ($ser->affected_rows == 1) {
                        $ser->query("UPDATE `tokens` SET `used`=CURRENT_TIMESTAMP AND `status`=false WHERE `token`='$token' AND `token-key`='$key' LIMIT 1;");
                        echo 'success';
                    } elseif ($ser->affected_rows == -1) {
                        echo "err_sql_query_failed";
                        trigger_error("SQL Query Failed: ".$ser->error);
                    } else {
                        echo "err_unknown_error";
                        trigger_error("Unknown Error Occurred");
                    }
                } elseif ($token_check->num_rows == 0) {
                    echo "err_invalid_token";
                    trigger_error("Invalid Signup Token");
                } elseif ($token_check->num_rows == -1) {
                    echo "err_sql_query_failed";
                    trigger_error("SQL Query Failed: ".$ser->error);
                } else {
                    echo "err_unknown_error";
                    trigger("Unknown Error Occurred");
                }
            }



        } elseif ($action == "checkEmail") {
            if (checkFields(['email'])) {
                $email = $ser->real_escape_string($_POST['email']);
                $search = $ser->query("SELECT 1 FROM `accounts` WHERE `email`='$email' LIMIT 1;");
                
                if ($search->num_rows > 0) {
                    echo "email_taken";
                } elseif ($search->num_rows == 0) {
                    echo "email_available";
                } else {
                    echo "err_sql_query_failed";
                    trigger_error("SQL Query Failed: ".$ser->error);
                }
            }
    
        
        
        } elseif ($action == "getUserDataFromToken") {
            if (checkFields(['token', 'key'])) {
                $token = $ser->real_escape_string($_POST['token']);
                $key = $ser->real_escape_string($_POST['key']);

                $query = $ser->query("SELECT `token-data` FROM `tokens` WHERE `token`='$token' AND `token-key`='$key' AND `used`=NULL LIMIT 1;");
                if ($query->num_rows == 1) {
                    $userData = $query->fetch_assoc()['token-data'];
                    echo $userData;
                    die;
                } else if ($query->num_rows == 0) { 
                    echo "err_invalid_token_or_key";
                    trigger_error("Bad Token or Token Key");
                } else if ($ser->affected_rows == -1) {
                    echo "err_sql_query_failed";
                    trigger_error("SQL Query Failed: ".$ser->error);
                } else {
                    echo "err_unknown_error";
                    trigger_error("Unknown Error Occurred");
                }
            } 
        }
    }
}

function uuid($lenght = 16) {
    if (function_exists("random_bytes")) {
        $bytes = random_bytes(ceil($lenght / 2));
    } elseif (function_exists("openssl_random_pseudo_bytes")) {
        $bytes = openssl_random_pseudo_bytes(ceil($lenght / 2));
    } else {
        throw new Exception("no cryptographically secure random function available");
    }
    return substr(bin2hex($bytes), 0, $lenght);
}

function checkFields($required_fields) {
    if (!empty($_POST)) {
        $missing_fields = [];
        foreach ($required_fields as $val) {if (!isset($_POST[$val])) {array_push($missing_fields, $val);}}
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
