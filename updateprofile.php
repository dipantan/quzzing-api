<?php
include './conn.php';
header('Content-Type: application/json');
error_reporting(E_ALL & ~E_NOTICE);
$case = $_POST['case'];
switch ($case) {

    case 'updateprofile':
        $mobile = $_POST['mobile'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $address = $_POST['address'];
        $gender = $_POST['gender'];
        $dob = $_POST['dob'];
        if (!isset($name) || !isset($email) || !isset($password)) {

            echo json_encode(array('code' => 'Field is empty'));

            exit;

        }
        $sql = "UPDATE user SET name = '$name', email = '$email', password = '$password',address = '$address', gender = '$gender', dob = '$dob' WHERE mobile = '$mobile'";
        $result= $conn->query($sql);
        if($result){
            $sqll = "SELECT * FROM user WHERE mobile = '$mobile'";

            $result_ = $conn->query($sqll);
            if ($result_->num_rows == 1){
                $row = $result_->fetch_assoc();
            }
            echo json_encode(array('code' => 'sucess','data'=>$row));
        }else{
            echo json_encode(array('code' => 'Failed'));
        }
    break;
    }
?>