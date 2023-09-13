<?php

error_reporting(E_ALL);
error_reporting(E_ERROR | E_PARSE );  
 error_reporting(0);  

include_once '../lib.php';



if(isset($_POST) && !empty($_POST)){

  $response = array();
  extract(array_map("test_input",$_POST));

  if($tag=='getCategory'){

    $q=$d->select("category","status='true'","ORDER BY category_id DESC");

    if (mysqli_num_rows($q) > 0) {
   
        $response["categoryList"] = array();
               
                       while($data_app=mysqli_fetch_array($q)) {
                             $categoryList=array();
                             $categoryList["category_id"]=$data_app["category_id"];
                             $categoryList["name"]=$data_app["name"];
                             array_push($response["categoryList"], $categoryList); 
                          }
   
   
                        $response["message"]="success.";
                        $response["status"]="200";
                        echo json_encode($response);
    
   
    }else{
                       $response["message"]="faild.";
                        $response["status"]="201";
                        echo json_encode($response); 
    }


  }elseif ($tag=="AddCategory"){

    $m->set_data('name',$category_name);
    $m->set_data('status',$category_status);
    $a = array('name'=>$m->get_data('name'),'status'=>$m->get_data('status'));

    $q= $d->insert("category",$a);
    $category_id = $con->insert_id;

    if($q==true){
        $response['category_id']=$category_id;
        $response['message']='Insert successfully';
        $response['status']=200;
        echo json_encode($response);
    }else{
        $response["message"]="faild.";
        $response["status"]="201";
        echo json_encode($response); 
    }

  }
  elseif ($tag=="UpdateCategory"){

    $m->set_data('name',$category_name);
    $m->set_data('status',$category_status);
    $a = array('name'=>$m->get_data('name'),'status'=>$m->get_data('status'));

    $q= $d->update("category",$a,"category_id=$category_id");

    if($q==true){
        $response['category_id']=$category_id;
        $response['message']='update successfully';
        $response['status']=200;
        echo json_encode($response);
    }else{
        $response["message"]="faild.";
        $response["status"]="201";
        echo json_encode($response); 
    }

  }

  else{
                       $response["message"]=" not in get url faild.";
                        $response["status"]="201";
                        echo json_encode($response); 
  }





}
?>
