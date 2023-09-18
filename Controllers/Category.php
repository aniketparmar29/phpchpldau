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


  }elseif ($tag == "AddCategory") {
    $m->set_data('name', $category_name);
    $m->set_data('status', $category_status);
    $a = array('name' => $m->get_data('name'), 'status' => $m->get_data('status'));

    $existingCategory = $d->select("category", "name='" . $m->get_data('name') . "'");
    
    if ($existingCategory && $existingCategory->num_rows > 0) {
        $response["message"] = "Category with the same name already exists.";
        $response["status"] = "201";
        echo json_encode($response);
    } else {
        $q = $d->insert("category", $a);
        $category_id = $con->insert_id;

        if ($q == true) {
            $response['category_id'] = $category_id;
            $response['message'] = 'Insert successfully';
            $response['status'] = 200;
            echo json_encode($response);
        } else {
            $response["message"] = "Failed.";
            $response["status"] = "201";
            echo json_encode($response);
        }
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

  }elseif($tag=="StatusCategory"){
    $m->set_data('status',$status);

    $a = array('status'=>$m->get_data('status'));

      $q = $d->update("category",$a,'');

      if($q==true){
        $response['message']="status become $status";
        $response['status']="200";
        echo json_encode($response);
      }else{
        $response['message']="faild";
        $response['status']="201";
        echo json_encode($response);
      }


  }elseif($tag=="DeleteCateogory"){
    $q=$d->delete("category","category_id=$category_id");
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
  if($tag=='getAllDetails'){

    $q=$d->select("category","status=1","ORDER BY category_id ASC");

    if (mysqli_num_rows($q) > 0) {
   
        $response["categoryList"] = array();
               
                       while($data_app=mysqli_fetch_array($q)) {
                             $categoryList=array();
                             $categoryList["category_id"]=$data_app["category_id"];
                             $categoryList["name"]=$data_app["name"];

                             $category_id=$data_app["category_id"];

                             $q2=$d->select("subcategory","category_id=$category_id","");

                             if (mysqli_num_rows($q2) > 0) {
                                $categoryList["subcategoryList"] = array();
                            
                                  while($data_app=mysqli_fetch_array($q2)) {
                                    $subcategoryList = array();
                                    $subcategoryList["sub_id"]=$data_app["sub_id"];
                                    $subcategoryList["category_id"]=$data_app["category_id"];
                                    $subcategoryList["name"]=$data_app["name"];

                                    $subcategory_id = $data_app["sub_id"];

                                    $q3=$d->select("product","sub_id=$subcategory_id","");

                                    if (mysqli_num_rows($q3) > 0) {
                                       $subcategoryList["productList"] = array();
                                   
                                         while($data_app=mysqli_fetch_array($q3)) {
                                           $productList = array();
                                           $productList["pro_id"]=$data_app["pro_id"];
                                           $productList["sub_id"]=$data_app["sub_id"];
                                           $productList["category_id"]=$data_app["category_id"];
                                           $productList["name"]=$data_app["name"];
                                           array_push($subcategoryList["productList"], $productList); 
                                         }
                                    }


                                   array_push($categoryList["subcategoryList"], $subcategoryList); 
                                  }
                             }


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


  }
  else{
                       $response["message"]=" not in get url faild.";
                        $response["status"]="201";
                        echo json_encode($response); 
  }





}
?>
