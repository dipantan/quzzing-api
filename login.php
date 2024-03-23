<?php

include './conn.php';

header('Content-Type: application/json');



$case = $_POST['case'];


function generateReferenceCode($length = 8)
{
    // Define characters that can be used in the reference code
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    // Get the total number of characters
    $charactersLength = strlen($characters);

    // Initialize the reference code variable
    $referenceCode = '';

    // Generate a random reference code
    for ($i = 0; $i < $length; $i++) {
        // Choose a random character from the available characters
        $referenceCode .= $characters[rand(0, $charactersLength - 1)];
    }

    return $referenceCode;
}


switch ($case) {

    case 'login':
        $mobile = $_POST['mobile'];
        if (!isset($mobile)) {

            echo json_encode(array('error' => true, 'message' => 'Enter a valid mobile number'));

            exit;
        }
        $sql = "SELECT * FROM user WHERE mobile = '$mobile' and name is not null and email is not null and password is not null";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            // user registered 
            $row = $result->fetch_assoc();

            if ($row) {

                echo json_encode(array('error' => false, 'code' => 'USER_REGISTERED', 'data' => $row));
            } else {

                echo json_encode(array('error' => true, 'message' => 'Mobile number not saved failed!'));
            }
        } else {
            // user not registered
            // $refCode = generateReferenceCode();
            $name = $_POST['name'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $refercode = $_POST['refercode'];

            $sql = "SELECT * FROM user WHERE mobile = '$mobile'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                if (!isset($name) || !isset($email) || !isset($password)) {
                    echo json_encode(array('error' => true, 'message' => 'Name Email Password is empty !'));
                    exit;
                }

                if (isset($refercode)) {
                    $sql = "select * from user where refer_code='$refercode'";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        $row = mysqli_fetch_assoc($result);
                        $amount = 50;
                        $mobile_num = $row['mobile'];

                        /* Start transaction */
                        mysqli_begin_transaction($conn);
                        try {
                            $sql1 = "update wallet set balance=balance+$amount where refer_code='$refercode'";
                            $sql2 = "INSERT INTO `transaction_history`(`amount`, `status`, `type`, `mobile`, `desc`) VALUES ($amount,'successful','credit',$mobile_num,'Received bonus for invite')";
                            $sql3 = "insert into referral_history (mobile,referral_id,refer_name) values ('$mobile_num', '$mobile','$name')";

                            $query1 = $conn->query($sql1);
                            $query2 = $conn->query($sql2);
                            $query3 = $conn->query($sql3);

                            /* If code reaches this point without errors then commit the data in the database */
                            mysqli_commit($conn);


                            $sql = "UPDATE user SET name = '$name', email = '$email', password = '$password' WHERE mobile = '$mobile'";
                            if ($conn->query($sql)) {
                                $sql = "SELECT name, email, password FROM user WHERE mobile = '$mobile' and name is not null and email is not null and password is not null";
                                $result = $conn->query($sql);
                                $row = $result->fetch_assoc();

                                if ($row) {
                                    $sqll = "SELECT * FROM user WHERE mobile = '$mobile'";

                                    $result_ = $conn->query($sqll);
                                    if ($result_->num_rows == 1) {
                                        $row = $result_->fetch_assoc();
                                    }
                                    echo json_encode(array('error' => false, 'code' => 'sucess', 'data' => $row));
                                } else {
                                    echo json_encode(array('error' => true, 'message' => 'Update failed!'));
                                }
                            };
                        } catch (mysqli_sql_exception $exception) {
                            mysqli_rollback($conn);
                            throw $exception;
                        }



                        $query1 = mysqli_query();
                    } else {
                        echo json_encode(array('error' => true, 'message' => 'Invalid refer code'));
                        exit;
                    }
                }


                $sql = "UPDATE user SET name = '$name', email = '$email', password = '$password' WHERE mobile = '$mobile'";
                if ($conn->query($sql)) {
                    $sql = "SELECT name, email, password FROM user WHERE mobile = '$mobile' and name is not null and email is not null and password is not null";
                    $result = $conn->query($sql);
                    $row = $result->fetch_assoc();
                    if ($row) {
                        $sqll = "SELECT * FROM user WHERE mobile = '$mobile'";

                        $result_ = $conn->query($sqll);
                        if ($result_->num_rows == 1) {
                            $row = $result_->fetch_assoc();
                        }
                        echo json_encode(array('error' => false, 'code' => 'sucess', 'data' => $row));
                    } else {
                        echo json_encode(array('error' => true, "message" => "User update failed!"));
                    }
                };
            }
            $refCode = generateReferenceCode();
            $amount = 50;
            $sql = "INSERT INTO user (mobile,refer_code) VALUES ('$mobile','$refCode')";
            $ress = $conn->query($sql);
            if ($ress) {
                $sql9 = "INSERT INTO `transaction_history`(`amount`, `status`, `type`, `mobile`, `desc`) VALUES ('50','successful','credit',$mobile,'Received bonus for signup')";
                $ress_ = $conn->query($sql9);
                if ($ress_) {
                    $sql_ = "INSERT INTO wallet (mobile,refer_code,balance) VALUES ('$mobile','$refCode',$amount)";
                    $res = $conn->query($sql_);
                    if ($res) {
                        echo json_encode(array('error' => false, 'status' => 'success', 'code' => "USER_NOT_REGISTERED"));
                    } else {
                        echo json_encode(array('error' => true, 'message' => 'Problem in adding Money!'));
                    }
                }
            }


            // setcookie('user', base64_encode(json_encode($row)), time() + (86400 * 365), "/"); // 86400 = 1 day
            // if (isset($_COOKIE['user'])) {

            // } else {
            //     echo json_encode(array('error' =>true, 'message' => 'User set failed!'));
            // }
        }
        break;

    case 'register':

        $name = $_POST['name'];

        $email = $_POST['email'];

        $password = $_POST['password'];



        if (!isset($name) || !isset($email) || !isset($password)) {

            echo json_encode(array('status' => 'fail'));

            exit;
        }



        $sql = "INSERT INTO user (name, email, password) VALUES ('$name', '$email', '$password')";

        $conn->query($sql);



        echo json_encode(array('status' => 'success'));

        break;



    default:

        echo json_encode(array('status' => 'fail'));

        break;
}
