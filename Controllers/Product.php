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
  
    $existingCategory = $d->select("product", "name='" . $m->get_data('name') . "'");
    
    if ($existingCategory && $existingCategory->num_rows > 0) {
        $response["message"] = "Product with the same name already exists.";
        $response["status"] = "201";
        echo json_encode($response);
    } else {

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

  }elseif($tag=="StatusProduct"){
    $m->set_data('status',$status);
    $a = array('status'=>$m->get_data('status'));
    
      $q = $d->update("product",$a,'');
      if($q==true){
        $response['message']="status become $status";
        $response['status']="200";
        echo json_encode($response);
      }else{
        $response['message']="faild";
        $response['status']="201";
        echo json_encode($response);
      }


  }elseif($tag=="DeleteProduct"){
    $q=$d->delete("product","pro_id=$product_id");
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
  elseif ($tag == 'getAllProduct') {
    $q = $d->select("product,subcategory,category", "product.sub_id=subcategory.sub_id AND category.category_id=subcategory.category_id");

    if ($q) { // Check if the query was successful
        $response["productList"] = array();

        while ($data_app = mysqli_fetch_assoc($q)) { // Use mysqli_fetch_assoc for associative arrays
            $productDetails = array();
            $productDetails["pro_id"] = $data_app["pro_id"];
            $productDetails["sub_id"] = $data_app["sub_id"];
            $productDetails["category_id"] = $data_app["category_id"];
            $productDetails["name"] = $data_app["name"];

            array_push($response["productList"], $productDetails);
        }

        $response["message"] = "Success.";
        $response["status"] = "200";
        echo json_encode($response);
    } else {
        $response["message"] = "Error fetching data.";
        $response["status"] = "500"; 
        echo json_encode($response);
    }
}

if ($tag == "addRating") {
  if (isset($user_id) && isset($product_id) && isset($rating)) {
      $existingRating = $d->select("product_rating", "user_id=$user_id AND pro_id=$product_id", "");

      if ($existingRating && mysqli_num_rows($existingRating) > 0) {
          $rating_data = mysqli_fetch_array($existingRating);
          $rating_id = $rating_data["pr_id"];
          $updatedRating = array('rating' => $rating);


          $q3 = $d->update("product_rating", $updatedRating, "rating_id=$rating_id");
          $qm = $d->select("product_rating", "pro_id=$product_id");
          $totalrating = 0;
          $numRatings = 0;

          while ($ratedData = mysqli_fetch_array($qm)) {
              $totalrating += $ratedData["rating"];
              $numRatings++;
          }

          $avg_rating = $numRatings > 0 ? $totalrating / $numRatings : 0;
          $updatedAVGRating = array('Avg_Rating' => $avg_rating);

          $qp = $d->update("product", $updatedAVGRating, "pro_id=$product_id");



          $response["message"] = "Your Rating Has Been Updated And Average Rating Updated in Product";
          $response["status"] = 200;
          echo json_encode($response);
          exit();
      } else {
          $product_ratingData = array(
              'user_id' => $user_id,
              'pro_id' => $product_id,
              'rating' => $rating,
          );
          $inserted = $d->insert("product_rating", $product_ratingData);

          $qm = $d->select("product_rating", "pro_id=$product_id");
          $totalrating = 0;
          $numRatings = 0;

          while ($ratedData = mysqli_fetch_array($qm)) {
              $totalrating += $ratedData["rating"];
              $numRatings++;
          }

          $avg_rating = $numRatings > 0 ? $totalrating / $numRatings : 0;
          $updatedAVGRating = array('Avg_Rating' => $avg_rating);

          $qp = $d->update("product", $updatedAVGRating, "pro_id=$product_id");

          if ($inserted) {
              $response["message"] = "Product_Rating added to Product successfully And Average Rating Updated in Product";
              $response["status"] = 200;
              echo json_encode($response);
          } else {
              $response["message"] = "Failed to add product_rating to product";
              $response["status"] = 201;
              echo json_encode($response);
          }
      }
  } else {
      $response["message"] = "Missing required parameters";
      $response["status"] = 201;
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
