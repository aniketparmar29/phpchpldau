<?php

error_reporting(E_ALL);
error_reporting(E_ERROR | E_PARSE );  
 error_reporting(0);  

include_once '../lib.php';



if(isset($_POST) && !empty($_POST)){

  $response = array();
  extract(array_map("test_input",$_POST));

 if($tag=='getProduct'){

    $q=$d->select("product","status='true'","ORDER BY pro_id DESC");

  if (mysqli_num_rows($q) > 0) {
 
      $response["productList"] = array();
             
                     while($data_app=mysqli_fetch_array($q)) {
                           $categoryList=array();
                           $categoryList["pro_id"]=$data_app["pro_id"];
                           $categoryList["sub_id"]=$data_app["sub_id"];
                           $categoryList["category_id"]=$data_app["category_id"];
                           $categoryList["name"]=$data_app["name"];
                           array_push($response["productList"], $categoryList); 
                        }
 
 
                      $response["message"]="success.";
                      $response["status"]="200";
                      echo json_encode($response);
  

  }
  
} 
elseif ($tag=="AddProduct"){

    $m->set_data('name',$product_name);
    $m->set_data('category_id',$product_categoryid);
    $m->set_data('sub_id',$product_subid);
    $m->set_data('price',$product_price);
    $m->set_data('status',$product_status);
    $a = array('name'=>$m->get_data('name'),'status'=>$m->get_data('status'),'category_id'=>$m->get_data('category_id'),'sub_id'=>$m->get_data('sub_id'),'price'=>$m->get_data('price'));
  
    $q= $d->insert("product",$a);
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
  
  }elseif ($tag=="UpdateProduct"){

    $m->set_data('name',$product_name);
    $m->set_data('status',$product_status);
    $a = array('name'=>$m->get_data('name'),'status'=>$m->get_data('status'));

    $q= $d->update("product",$a,"pro_id=$pro_id");

    if($q==true){
        $response['pro_id']=$pro_id;
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
