<?php
include '../conn.php';
ini_set('display_errors', 1);

ini_set('display_startup_errors', 1);

error_reporting(E_ALL);

$case = $_REQUEST['case'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    switch ($case) {
        case 'add_state':
            $name = $_POST['name'];
            $image = $_POST['image'];
            $sql = "insert into states (state_name,image) values ('$name', '$image')";
            $result = mysqli_query($conn, $sql);
            if ($result) {
                echo json_encode(array('error' => false, 'message' => 'State Added Successfully'));
            } else {
                echo json_encode(array('error' => true, 'message' => 'Something Went Wrong'));
            }
            break;

        case 'delete_state':
            $id = $_POST['id'];
            $sql = "delete from states where state_id='$id'";
            $result = mysqli_query($conn, $sql);
            if ($result) {
                echo json_encode(array('error' => false, 'message' => 'State Deleted Successfully'));
            } else {
                echo json_encode(array('error' => true, 'message' => 'Something Went Wrong'));
            }
            break;

        case 'add_subject':
            $name = $_POST['name'];
            $image = $_POST['image'];
            $state = $_POST['state'];
            $sql = "insert into subject (name,image,state) values ('$name', '$image', '$state')";
            $result = mysqli_query($conn, $sql);
            if ($result) {
                echo json_encode(array('error' => false, 'message' => 'Subject Added Successfully'));
            } else {
                echo json_encode(array('error' => true, 'message' => 'Something Went Wrong'));
            }
            break;

        case 'delete_subject':
            $id = $_POST['id'];
            $sql = "delete from subject where id='$id'";
            $result = mysqli_query($conn, $sql);
            if ($result) {
                echo json_encode(array('error' => false, 'message' => 'Subject Added Successfully'));
            } else {
                echo json_encode(array('error' => true, 'message' => 'Something Went Wrong'));
            }
            break;

        case 'fetch_exam':
            $sql = "select * from exams";
            $result = mysqli_query($conn, $sql);
            if ($result) {
                $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
                echo json_encode(array('error' => false, 'data' => $data));
            }
            break;

        case 'add_exam':
            $name = $_POST['name'];
            $image = $_POST['image'];
            $sql = "insert into exams (name,image) values ('$name', '$image')";
            $result = mysqli_query($conn, $sql);
            if ($result) {
                echo json_encode(array('error' => false, 'message' => 'Exam Added Successfully'));
            }
            break;

        case 'delete_exam':
            $id = $_POST['id'];
            $sql = "delete from exams where id='$id'";
            $result = mysqli_query($conn, $sql);
            if ($result) {
                echo json_encode(array('error' => false, 'message' => 'Exam Deleted Successfully'));
            }
            break;

        case 'fetch_series':
            $sql = "select * from series";
            $result = mysqli_query($conn, $sql);
            if ($result) {
                $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
                echo json_encode(array('error' => false, 'data' => $data));
            }
            break;
            
        case 'add_series':
            $exam_id = $_POST['exam_id'];
            $model_id = $_POST['model_id'];
            $name = $_POST['name'];
            $image = $_POST['image'];
            $fee = $_POST['fee'];
            $sql = "insert into series (exam_id,model_id,name,image,fee) values ('$exam_id', '$model_id', '$name', '$image', '$fee')";
            $result = mysqli_query($conn, $sql);
            if ($result) {
                echo json_encode(array('error' => false, 'message' => 'Series Added Successfully'));
            } else {
                echo json_encode(array('error' => false, 'message' => 'Something Went Wrong'));
            }
            break;
            
        case 'delete_series':
            $id = $_POST['id'];
            $sql = "delete from series where id='$id'";
            $result = mysqli_query($conn, $sql);
            if ($result) {
                echo json_encode(array('error' => false, 'message' => 'Series Deleted Successfully'));
            } else {
                echo json_encode(array('error' => false, 'message' => 'Something Went Wrong'));
            }
            break;
        default:
            break;
    }
} else {
    http_response_code(405);
    echo json_encode(array('status' => 405, 'msg' => 'Method Not Allowed'));
}
