<?php

error_reporting(E_ALL);
error_reporting(E_ERROR | E_PARSE );  
 error_reporting(0);  

include_once '../lib.php';



if(isset($_POST) && !empty($_POST)){

  $response = array();
  extract(array_map("test_input",$_POST));

 if($tag=='getSubCategory'){

    $q=$d->select("subcategory","status='true' AND category_id=$category_id","ORDER BY sub_id DESC");

  if (mysqli_num_rows($q) > 0) {
 
      $response["subcategoryList"] = array();
             
                     while($data_app=mysqli_fetch_array($q)) {
                           $categoryList=array();
                           $categoryList["sub_id"]=$data_app["sub_id"];
                           $categoryList["category_id"]=$data_app["category_id"];
                           $categoryList["name"]=$data_app["name"];
                           array_push($response["subcategoryList"], $categoryList); 
                        }
 
 
                      $response["message"]="success.";
                      $response["status"]="200";
                      echo json_encode($response);
  

  }
  
} elseif ($tag=="AddSubCategory"){

  $m->set_data('name',$subcategory_name);
  $m->set_data('category_id',$subcategory_categoryid);
  $m->set_data('status',$subcategory_status);
  $a = array('name'=>$m->get_data('name'),'status'=>$m->get_data('status'),'category_id'=>$m->get_data('category_id'));

  $existingCategory = $d->select("subcategory", "name='" . $m->get_data('name') . "'");
    
  if ($existingCategory && $existingCategory->num_rows > 0) {
      $response["message"] = "SubCategory with the same name already exists.";
      $response["status"] = "201";
      echo json_encode($response);
  } else {
    
      $q= $d->insert("subcategory",$a);
      $sub_id = $con->insert_id;
    
      if($q==true){
          $response['sub_id']=$sub_id;
          $response['message']='Insert successfully';
          $response['status']=200;
          echo json_encode($response);
      }else{
          $response["message"]="faild.";
          $response["status"]="201";
          echo json_encode($response); 
      }
  }


}elseif ($tag=="UpdateSubCategory"){

  $m->set_data('name',$subcategory_name);
  $m->set_data('status',$subcategory_status);
  $a = array('name'=>$m->get_data('name'),'status'=>$m->get_data('status'));

  $q= $d->update("subcategory",$a,"sub_id=$sub_id");

  if($q==true){
      $response['sub']=$sub_id;
      $response['message']='update successfully';
      $response['status']=200;
      echo json_encode($response);
  }else{
      $response["message"]="faild.";
      $response["status"]="201";
      echo json_encode($response); 
  }

}elseif($tag=="StatusSubCategory"){
  $m->set_data('status',$status);
  $a = array('status'=>$m->get_data('status'));
  
    $q = $d->update("subcategory",$a,'');
    if($q==true){
      $response['message']="status become $status";
      $response['status']="200";
      echo json_encode($response);
    }else{
      $response['message']="faild";
      $response['status']="201";
      echo json_encode($response);
    }


}
elseif($tag=="DeleteSubCategory"){
  $q=$d->delete("subcategory","sub_id=$subcategory_id");
  if($q==true){
    $response['message']="deleted successfully";
    $response['status']="200";
    echo json_encode($response);
  }else{
    $response['message']="faild";
    $response['status']="201";
    echo json_encode($response);
  }

}
  else{
                       $response["message"]="wrong tag";
                        $response["status"]="201";
                        echo json_encode($response); 
  }





}
else{
  $response["message"]=" not in get url faild.";
   $response["status"]="201";
   echo json_encode($response); 
}

?>
