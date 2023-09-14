<?php

error_reporting(E_ALL);
error_reporting(E_ERROR | E_PARSE );  
 error_reporting(0);  

include_once '../lib.php';



if(isset($_POST) && !empty($_POST)){

  $response = array();
  extract(array_map("test_input",$_POST));

  if ($tag == 'Login') {
    $q = $d->select("users", "email='$email' AND password='$password'", "");

    if (mysqli_num_rows($q) > 0) {
        $response["usersList"] = array();

        while ($data_app = mysqli_fetch_array($q)) {
            $userDetails = array();
            $userDetails["first_name"] = $data_app["first_name"]; 
            $userDetails["last_name"] = $data_app["last_name"];  
            $userDetails["email"] = $data_app["email"];
            $userDetails["created_at"] = $data_app["created_at"];
            array_push($response["usersList"], $userDetails);
        }

        $response["message"] = "Login success.";
        $response["status"] = "200";
        echo json_encode($response);
    } else {
        $response["message"] = "Wrong Credentials.";
        $response["status"] = "201";
        echo json_encode($response);
    }
}
elseif ($tag=="Register"){

    $m->set_data('first_name',$first_name);
    $m->set_data('last_name',$last_name);
    $m->set_data('email',$email);
    $m->set_data('password',$password);
    $a = array('first_name'=>$m->get_data('first_name'),'last_name'=>$m->get_data('last_name'),'email'=>$m->get_data('email'),'password'=>$m->get_data('password'));
  
    $existingemail = $d->select("users", "email='" . $m->get_data('email') . "'");
    
    if ($existingemail && $existingemail->num_rows > 0) {
        $response["message"] = "Email with the same Email already exists.";
        $response["status"] = "201";
        echo json_encode($response);
    } else {

      $q= $d->insert("users",$a);
      $sub_id = $con->insert_id;
    
      if($q==true){
          $response['sub_id']=$sub_id;
          $response['message']='Register successfully';
          $response['status']=200;
          echo json_encode($response);
      }else{
          $response["message"]="faild.";
          $response["status"]="201";
          echo json_encode($response); 
      }
    }



  
  }
  else{
                       $response["message"]=" not in get url faild.";
                        $response["status"]="201";
                        echo json_encode($response); 
  }





}
?>
