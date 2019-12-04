<?php
@session_start();

$ini = parse_ini_file('../../bin/config.ini');
if ($ini['debug']) {error_reporting(E_ALL);} else {error_reporting(0);}
set_include_path($ini['include_path']);

$ser = new MySQLi($ini['host'], $ini['username'], $ini['password'], $ini['database']);
if ($ser->connect_errno) {
    echo "err_mysql_server_connection";
} else {
    if (isset($_GET['action'])) {
        $action = $_GET['action'];

        if ($action == "getTrips") {
            $query = $ser->query("SELECT * FROM `trips`;");
            if (is_bool($query) && !$query) {
                echo "err_sql_query_failed";
                trigger_error("SQL Query Error: ".$ser->error);
            } else {
                $response = "";
                for ($trip=0; $trip < $query->num_rows; $trip++) { 
                    $tripdata = $query->fetch_assoc();
                    $tripid = $tripdata['trip-id'];
                    $signup_link = $tripdata['signup-link'];
                    $signups = json_decode($tripdata['signup-list']);
                    $signed_up_status = false;
                       
                    if (isset($_SESSION['uuid'])) {
                        foreach ($signups as $value) {
                            if ($value == $_SESSION['uuid']) 
                                {$signed_up_status = true;}
                        }
                    }

                    $trip_start_date = date("jS", strtotime($tripdata['trip-start-date']));
                    $trip_end_date = date("jS F, Y", strtotime($tripdata['trip-end-date']));
                    $desc = $tripdata['short-desc'];
                    $banner_image = $tripdata['banner-image'];
                    $trip_title = $tripdata['name'];
                    $trip_buttons = "<a href='https://lifeinthestudio/trips/$trip.html' class=\"read-more-btn\">Read More</a>";
                    if ($signed_up_status) {
                        $trip_buttons .= "<a href=\"javascript:void();\" id=\"signup-{$trip}\" class=\"trip-signup-btn\" disabled>You are already signed up</a>";
                    } else {
                        if ($signup_link == "internal") {$trip_buttons = "<a href=\"javascript:signup($tripid, $(this));\" id=\"signup-{$trip}\" class=\"trip-signup-btn\">Signup</a>";} 
                        else {$trip_buttons .= "<a href=\"$signup_link\" id=\"signup-{$trip}\" class=\"signup-btn\">Signup</a>";}
                    }

                    $response = $response."
                    <div class=\"trip_main_item\" id='trip-{$tripid}'>
                        <div class=\"trip_img\">
                            <img class=\"img-fluid\" src=\"{$banner_image}\" alt=\"\">
                            <div class=\"trip_title\"><a href=\"javascript:void()\" class=\"title\"><h4>{$trip_title}</h4></a></div>
                        </div>
                        <div class=\"trip_text\">
                            <div class='trip_date'>
                            <span>{$trip_start_date}</span> - <span>{$trip_end_date}</span>
                            </div>
                            <div><p>{$desc}</p></div>
                            <nav class='trip-buttons'>
                                {$trip_buttons}
                            </nav>
                        </div>
                    </div>";

                }
                echo $response;
            }
        } else {
            echo "err_bad_action";
        }
    }
}
?>