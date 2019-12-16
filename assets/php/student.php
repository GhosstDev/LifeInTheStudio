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

        if ($action == 'inviteStudent') {
            if (checkFields(['name', 'email'])) {
                $token = hash("sha384", json_encode($_POST));
                $token_key = hash('gost', random_bytes(128));
                $ser->query("INSERT INTO `signup-tokens` (token, 'token-key', 'type', 'token-data') VALUES ('$token', '$token_key', 'signup', '{$ser->escape_string(json_encode($_POST))}');");
                if ($ser->affected_rows == -1) {
                    if ($ser->errno == 1062) {
                        echo "err_duplicate_user";
                    } else {
                        echo "err_sql_query_failed";
                        trigger_error("SQL Query Failed: ".$ser->error);
                    }
                } else {
                    $emailTemplate = @file_get_contents("/bin/email templates/student-invite-email.html", true);
                    if ($emailTemplate === false) {
                        echo "err_email_template_failed_to_read";
                        trigger_error(error_get_last());
                    } else {
                        $link = "https://lifeinthestudio.com/signup.html?action=confirmSignup&token=$token&key=$token_key";
                        $newEmailBody = str_replace("confirmAccountLink", $link, $emailTemplate);
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
            }
        
        
        
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
