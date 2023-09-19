<?php
error_reporting(E_ALL);
error_reporting(E_ERROR | E_PARSE);
error_reporting(0);

include_once '../lib.php';

if (isset($_POST) && !empty($_POST)) {
    $response = array();
    extract(array_map("test_input", $_POST));

    if ($tag == 'getCategory') {
        $q = $d->select("category", "", "");
        $response["categoryList"] = array();

        if (mysqli_num_rows($q) > 0) {
            while ($data_app = mysqli_fetch_array($q)) {
                $categoryList = array();
                $categoryList["category_id"] = $data_app["category_id"];
                $categoryList["name"] = $data_app["name"];
                $categoryList["status"] = $data_app["status"];
                array_push($response["categoryList"], $categoryList);
            }

            $response["message"] = "success";
            $response["status"] = 200;
        } else {
            $response["message"] = "no data found";
            $response["status"] = 201;
        }
    } 
    elseif ($tag == "getSingleCategory") {
        $q = $d->select("category", "category_id=$category_id", "");
        $response["categoryList"] = array();

        if (mysqli_num_rows($q) > 0) {
            while ($data_app = mysqli_fetch_array($q)) {
                $categoryList = array();
                $categoryList["category_id"] = $data_app["category_id"];
                $categoryList["name"] = $data_app["name"];
                $categoryList["status"] = $data_app["status"];
                array_push($response["categoryList"], $categoryList);
            }

            $response["message"] = "success";
            $response["status"] = 200;
        } else {
            $response["message"] = "no data found";
            $response["status"] = 201;
        }
    }
    elseif ($tag == "AddCategory") {
        $m->set_data('name', $category_name);
        $m->set_data('status', $category_status);
        $a = array('name' => $m->get_data('name'), 'status' => $m->get_data('status'));

        $existingCategory = $d->select("category", "name='" . $m->get_data('name') . "'");

        if ($existingCategory && $existingCategory->num_rows > 0) {
            $response["message"] = "Category with the same name already exists";
            $response["status"] = 201;
        } else {
            $q = $d->insert("category", $a);
            $category_id = $con->insert_id;

            if ($q == true) {
                $response['category_id'] = $category_id;
                $response['message'] = 'Insert successfully';
                $response['status'] = 200;
            } else {
                $response["message"] = "Failed to insert";
                $response["status"] = 201;
            }
        }
    } elseif ($tag == "UpdateCategory") {
        $m->set_data('name', $category_name);
        $m->set_data('status', $category_status);
        $a = array('name' => $m->get_data('name'), 'status' => $m->get_data('status'));

        $q = $d->update("category", $a, "category_id=$category_id");

        if ($q == true) {
            $response['category_id'] = $category_id;
            $response['message'] = 'Update successfully';
            $response['status'] = 200;
        } else {
            $response["message"] = "Failed to update";
            $response["status"] = 201;
        }
    } elseif ($tag == "StatusCategory") {
        $m->set_data('status', $status);
        $a = array('status' => $m->get_data('status'));

        $q = $d->update("category", $a, '');

        if ($q == true) {
            $response['message'] = "Status become $status";
            $response['status'] = 200;
        } else {
            $response['message'] = "Failed to update status";
            $response['status'] = 201;
        }
    } elseif ($tag == "DeleteCategory") {
        $q = $d->delete("category", "category_id=$category_id");

        if ($q == true) {
            $response['message'] = "Deleted successfully";
            $response['status'] = 200;
        } else {
            $response['message'] = "Failed to delete";
            $response['status'] = 201;
        }
    } elseif ($tag == 'getAllDetails') {
        $q = $d->select("category", "status=1", "ORDER BY category_id ASC");
        $response["categoryList"] = array();

        if (mysqli_num_rows($q) > 0) {
            while ($data_app = mysqli_fetch_array($q)) {
                $categoryList = array();
                $categoryList["category_id"] = $data_app["category_id"];
                $categoryList["name"] = $data_app["name"];
                $category_id = $data_app["category_id"];

                $q2 = $d->select("subcategory", "category_id=$category_id", "");
                $categoryList["subcategoryList"] = array();

                if (mysqli_num_rows($q2) > 0) {
                    while ($data_app = mysqli_fetch_array($q2)) {
                        $subcategoryList = array();
                        $subcategoryList["sub_id"] = $data_app["sub_id"];
                        $subcategoryList["category_id"] = $data_app["category_id"];
                        $subcategoryList["name"] = $data_app["name"];
                        $subcategory_id = $data_app["sub_id"];

                        $q3 = $d->select("product", "sub_id=$subcategory_id", "");
                        $subcategoryList["productList"] = array();

                        if (mysqli_num_rows($q3) > 0) {
                            while ($data_app = mysqli_fetch_array($q3)) {
                                $productList = array();
                                $productList["pro_id"] = $data_app["pro_id"];
                                $productList["sub_id"] = $data_app["sub_id"];
                                $productList["category_id"] = $data_app["category_id"];
                                $productList["name"] = $data_app["name"];
                                array_push($subcategoryList["productList"], $productList);
                            }
                        }

                        array_push($categoryList["subcategoryList"], $subcategoryList);
                    }
                }

                array_push($response["categoryList"], $categoryList);
            }

            $response["message"] = "success";
            $response["status"] = 200;
        } else {
            $response["message"] = "no data found";
            $response["status"] = 201;
        }
    } else {
        $response["message"] = "Invalid request";
        $response["status"] = 201;
    }

    echo json_encode($response);
}
?>
