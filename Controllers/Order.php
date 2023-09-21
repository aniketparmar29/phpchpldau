<?php 
error_reporting(E_ALL);
error_reporting(E_ERROR | E_PARSE);  
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

        if ($tag == "addtocart") {
            if (isset($user_id) && isset($product_id) && isset($qnty)) {
                $existingCart = $d->select("cart,cart_item,product", "user_id=$user_id", "");

                if ($existingCart && mysqli_num_rows($existingCart) > 0) {
                    $cartData = mysqli_fetch_array($existingCart);
                    $cart_id = $cartData['cart_id'];
                } else {
                    $cartData = array(
                        'user_id' => $user_id,
                        'create_at' => date('Y-m-d H:i:s')
                    );
                    $q = $d->insert("cart", $cartData);
                    $cart_id = $con->insert_id;
                }

                $productData = $d->select("product", "pro_id=$product_id", "");

                if ($productData && mysqli_num_rows($productData) > 0) {
                    $productInfo = mysqli_fetch_array($productData);
                    $category_id = $productInfo['category_id'];
                    $sub_id = $productInfo['sub_id'];
                } else {
                    $response["message"] = "Product not found";
                    $response["status"] = 201;
                    echo json_encode($response);
                    exit;
                }

                $existingCartItem = $d->select("cart_item", "cart_id=$cart_id AND pro_id=$product_id", "");

                if ($existingCartItem && mysqli_num_rows($existingCartItem) > 0) {
                    $cartItemData = mysqli_fetch_array($existingCartItem);
                    $newQuantity = $cartItemData['product_qnty'] + $qnty;
                    $updateData = array('product_qnty' => $newQuantity);
                    $updated = $d->update("cart_item", $updateData, "cart_item_id=" . $cartItemData['cart_item_id']);

                    if ($updated) {
                        $response["message"] = "Quantity updated successfully";
                        $response["status"] = 200;
                        echo json_encode($response);
                    } else {
                        $response["message"] = "Failed to update quantity";
                        $response["status"] = 201;
                        echo json_encode($response);
                    }
                } else {
                    $cartItemData = array(
                        'cart_id' => $cart_id,
                        'pro_id' => $product_id,
                        'product_qnty' => $qnty,
                        'category_id' => $category_id,
                        'sub_id' => $sub_id,
                        'added' => date('Y-m-d H:i:s')
                    );
                    $inserted = $d->insert("cart_item", $cartItemData);

                    if ($inserted) {
                        $response["message"] = "Product added to cart successfully";
                        $response["status"] = 200;
                        echo json_encode($response);
                    } else {
                        $response["message"] = "Failed to add product to cart";
                        $response["status"] = 201;
                        echo json_encode($response);
                    }
                }
            } else {
                $response["message"] = "Missing required parameters";
                $response["status"] = 201;
                echo json_encode($response);
            }
        }  else if ($tag == "getCartUser") {
            if (isset($user_id)) {
                $totalPrice = 0;
                $cartItemsData = array();
            
                $existingCart = $d->select("cart,cart_item,product", "cart.cart_id=cart_item.cart_id AND product.pro_id=cart_item.pro_id", "");
            
                if ($existingCart && mysqli_num_rows($existingCart) > 0) {
                    $cartData = mysqli_fetch_array($existingCart);
                    $cart_id = $cartData['cart_id'];
            
                    // $cartItems = $d->select("cart_item", "cart_id=$cart_id", "");
            
                    if ($existingCart && mysqli_num_rows($existingCart) > 0) {
                        while ($cartItemData = mysqli_fetch_array($existingCart)) {
                            $product_id = $cartItemData['pro_id'];
                            $quantity = $cartItemData['product_qnty'];
                            $productData = $d->select("product", "pro_id=$product_id", "");
            
                            if ($productData && mysqli_num_rows($productData) > 0) {
                                $productInfo = mysqli_fetch_array($productData);
                                $productPrice = $productInfo['price'];
                                $productTotal = $productPrice * $quantity;
                                $totalPrice += $productTotal;
                                $cartItemDetails = array(
                                    "product_id" => $product_id,
                                    "product_name" => $productInfo['name'],
                                    "quantity" => $quantity,
                                    "product_price" => $productPrice,
                                    "total_price" => $productTotal,
                                );
                                array_push($cartItemsData, $cartItemDetails);
                            }
                        }
            
                        $response["message"] = "Cart total calculated successfully";
                        $response["status"] = 200;
                        $response["total"] = $totalPrice;
                        $response["cartItems"] = $cartItemsData;
                        echo json_encode($response);
                    } else {
                        $response["message"] = "No cart items found for this user";
                        $response["status"] = 201;
                        echo json_encode($response);
                    }
                } else {
                    $response["message"] = "No cart found for this user";
                    $response["status"] = 201;
                    echo json_encode($response);
                }
            } else {
                $response["message"] = "user_id is required";
                $response["status"] = 201;
                echo json_encode($response);
            }
        } else {
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