<?php

error_reporting(E_ALL);
error_reporting(E_ERROR | E_PARSE );  
 error_reporting(0);  

 include_once '../lib.php';

session_start();

if (isset($_POST) && !empty($_POST)) {
    $response = array();
    extract(array_map("test_input", $_POST));

    if (empty($tag)) {
        $response["message"] = "Tag is required.";
        $response["status"] = "400";
        echo json_encode($response);
    } else {
        $tag = trim($tag);
        $first_name = trim($first_name);
        $last_name = trim($last_name);
        $email = trim($email);
        $password = trim($password);

        if ($tag == 'Login') {
            if (empty($email) || empty($password)) {
                $response["message"] = "Email and password are required.";
                $response["status"] = "400";
                echo json_encode($response);
            } else {
                // Set the time zone in PHP
                date_default_timezone_set("Asia/Kolkata");
                
                // Set the time zone in MySQL
                mysqli_query($con, "SET time_zone = 'Asia/Kolkata'");
                
                // Get the current login attempts and last attempt time from the users table
                $getUserQuery = "SELECT id, login_attempts, last_attempt_time FROM users WHERE email='$email'";
                $getUserResult = mysqli_query($con, $getUserQuery);
                
                if ($getUserResult && mysqli_num_rows($getUserResult) > 0) {
                    $userData = mysqli_fetch_assoc($getUserResult);
                    $userId = $userData['id'];
                    $loginAttempts = $userData['login_attempts'];
                    $lastAttemptTime = strtotime($userData['last_attempt_time']);
                    $currentTimestamp = time();
                    // echo $currentTimestamp-$lastAttemptTime;
                    $hourInSeconds = 3600; // 1 hour
                    
                    // Check if the user has reached 3 or more failed attempts within the last hour
                    if ($loginAttempts >= 3 && ($currentTimestamp - $lastAttemptTime) < $hourInSeconds) {
                        $response["message"] = "Too many login attempts. Please try again in one hour.";
                        $response["status"] = "429"; // HTTP 429 indicates too many requests
                        echo json_encode($response);
                    } else {
                        $q = $d->select(
                            "users u 
                            LEFT JOIN addresses a ON u.id = a.user_id", 
                            "u.email='$email' AND u.password='$password'", 
                            ""
                        );
                
                        if (mysqli_num_rows($q) > 0) {
                            mysqli_query($con, "UPDATE users SET login_attempts=0, last_attempt_time=NULL WHERE id=$userId");
                
                            $response["userDetails"] = array();
                
                            while ($data_app = mysqli_fetch_array($q)) {
                                $userDetails = array();
                                $userDetails["first_name"] = $data_app["first_name"];
                                $userDetails["last_name"] = $data_app["last_name"];
                                $userDetails["email"] = $data_app["email"];
                                $userDetails["created_at"] = $data_app["created_at"];
                
                                // Add addresses to the user details
                                $userDetails["addresses"] = array();
                                if (!empty($data_app["address_id"])) {
                                    $address = array(
                                        "add_type" => $data_app["add_type"],
                                        "address" => $data_app["address"],
                                        "zip" => $data_app["zip"],
                                        "landmark" => $data_app["landmark"]
                                    );
                                    $userDetails["addresses"][] = $address;
                                }
                
                                array_push($response["userDetails"], $userDetails);
                            }
                
                            $response["message"] = "Login success.";
                            $response["status"] = "200";
                            echo json_encode($response);
                           } else {
                            mysqli_query($con, "UPDATE users SET login_attempts=login_attempts+1, last_attempt_time=NOW() WHERE id=$userId");
            
                            $response["message"] = "Wrong Credentials.";
                            $response["status"] = "201";
                            echo json_encode($response);
                        }
                    }
                } else {
                    // User not found
                    $response["message"] = "User not found.";
                    $response["status"] = "404";
                    echo json_encode($response);
                }
            }
        }
        elseif ($tag == "Register") {
            if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
                $response["message"] = "All fields are required for registration.";
                $response["status"] = "400";
                echo json_encode($response);
            } else {
                $m->set_data('first_name', $first_name);
                $m->set_data('last_name', $last_name);
                $m->set_data('email', $email);
                $m->set_data('password', $password);
                $a = array(
                    'first_name' => $m->get_data('first_name'),
                    'last_name' => $m->get_data('last_name'),
                    'email' => $m->get_data('email'),
                    'password' => $m->get_data('password')
                );

                $existingemail = $d->select("users", "email='" . $m->get_data('email') . "'");

                if ($existingemail && $existingemail->num_rows > 0) {
                    $response["message"] = "Email with the same Email already exists.";
                    $response["status"] = "201";
                    echo json_encode($response);
                } else {
                    $q = $d->insert("users", $a);
                    $sub_id = $con->insert_id;

                    if ($q == true) {
                        $response['sub_id'] = $sub_id;
                        $response['message'] = 'Register successfully';
                        $response['status'] = 200;
                        echo json_encode($response);
                    } else {
                        $response["message"] = "Registration failed.";
                        $response["status"] = "201";
                        echo json_encode($response);
                    }
                }
            }
        }elseif ($tag == "AddAddress") {
            if (empty($user_id) || empty($add_type) || empty($address) || empty($zip) || empty($landmark)) {
                $response["message"] = "All fields are required for adding an address.";
                $response["status"] = "400";
                echo json_encode($response);
            } else {
                // Insert the address into the addresses table
                $user_id = intval($user_id);
                $m->set_data('add_type', $add_type);
                $m->set_data('address', $address);
                $m->set_data('zip', $zip);
                $m->set_data('landmark', $landmark);
                
                $a = array(
                    'user_id' => $user_id,
                    'add_type' => $m->get_data('add_type'),
                    'address' => $m->get_data('address'),
                    'zip' => $m->get_data('zip'),
                    'landmark' => $m->get_data('landmark')
                );
                
                $q = $d->insert("addresses", $a);
                
                if ($q == true) {
                    $response['message'] = 'Address added successfully.';
                    $response['status'] = 200;
                    echo json_encode($response);
                } else {
                    $response["message"] = "Failed to add the address.";
                    $response["status"] = "201";
                    echo json_encode($response);
                }
            }
        }
         else {
            $response["message"] = "Invalid tag.";
            $response["status"] = "400";
            echo json_encode($response);
        }
    }
} else {
    $response["message"] = "No data received.";
    $response["status"] = "400";
    echo json_encode($response);
}
?>
