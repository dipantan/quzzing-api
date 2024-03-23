<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include './conn.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$case = $_REQUEST['case'];

if ($_SERVER['REQUEST_METHOD'] == "GET" || $_SERVER['REQUEST_METHOD'] == "POST") {
    switch ($case) {
        
        case 'fetchrankbypool':
            $pool = $_GET['pool_id'];
            $query = "SELECT * FROM `rank` WHERE pool_id='$pool' ORDER BY result DESC ";
            $result = mysqli_query($conn, $query);
            if ($result) {
                $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
                echo json_encode(["error" => false, "data" => $data]);
            } else {
                echo json_encode(["error" => true, "message" => "Result Not publish yet!"]);
            }
        break;
        
        case 'getverified':
            $pid = $_POST['poolid'];

            $mobile = $_POST['mobile'];
            
            $sql = "SELECT * FROM rank WHERE pool_id = '$pid' AND number = '$mobile'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
             echo json_encode(["error" => true ,"message" => "Already Submitted !"]);
             return;
          } else {
                echo json_encode(["error" => false ,"message" => "Starting Your Exam."]);
             }
            break;



        case 'scoreupdate':
            $pid = $_POST['poolid'];

            $score = sprintf("%02d", $_POST['score']); //prepend leading zero before single digit number

            $ans = $_POST['user_ans'];

            $mobile = $_POST['mobile'];
            
            $asnwar = json_encode($ans);
            
            $sql = "SELECT * FROM rank WHERE pool_id = '$pid' AND number = '$mobile'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
             echo json_encode(["error" => true ,"message" => "Already Submitted !"]);
             return;
          } else {

            $sql = "INSERT INTO `rank`(
                        `pool_id`,
                        `result`,
                        `number`,
                        `user_answar`) VALUES(
                        '$pid',
                        '$score',
                        '$mobile',
                        '$asnwar')";
            $result = mysqli_query($conn, $sql);
            if ($result) {
                echo json_encode(["error" => false ,"message" => "Your Answar submitted sucessfull."]);
            }
          }
            break;
            
            
            case 'scoreupdatesreies':
            $point = $_POST['point'];

            $mobile = $_POST['mobile'];
            
            $sql = "UPDATE user SET point = point + $point WHERE mobile = '$mobile'";
            $result = $conn->query($sql);
            if ($result) {
             echo json_encode(["error" => false ,"message" => "Thanks for participate \n Your point is sucessfully updated."]);
          } else {

                echo json_encode(["error" => true ,"message" => "fail to update point"]);
          }
            break;
            
            
            
            
            
            
    }
} else {
    echo json_encode(['error' => 'Method not allowed']);
}

function walletUpdate($user_id, $amount): bool
{
    global $conn;
    $status = true;
    $amount = $_POST['amount'];
    if (empty($user_id) || empty($amount)) {
        $response = array(
            'success' => false,
            'message' => 'Number not Matched!'
        );
        echo json_encode($response);
        exit;
    }
    $sqll = "UPDATE wallet SET balance = balance - $amount WHERE mobile = '$user_id'";
    $result_ = $conn->query($sqll);
    if ($result_ && $conn->affected_rows > 0) {
        $sqlll = "INSERT INTO `transaction_history`(`amount`, `status`, `type`, `mobile`, `desc`) VALUES ($amount,'successful','debit',$user_id,'Pool Entry Fee')";
        $resultt = $conn->query($sqlll);
        if ($resultt) {
            $status = true;
        } else {
            $status = false;
        }
    } else {
        $status = false;
    }
    return $status;
}

function checkPoolSize($id)
{
    global $conn;
    $sql = "select * from pool where id='$id'";
    $result = mysqli_query($conn, $sql);
    $data = mysqli_fetch_assoc($result);
    if ($data['total_seats'] >= $data['available_seats']) {
        return true;
    } else {
        return false;
    }
}

function updateAvailableSeats($id)
{
    global $conn;
    $sql = "update pool set available_seats=available_seats+1 where id='$id'";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        return true;
    } else {
        return false;
    }
}

function checkWalletBalance($mobile)
{
    global $conn;
    $sqll = "SELECT * FROM wallet WHERE mobile = '$mobile'";
    $result_ = $conn->query($sqll);
    if ($result_->num_rows == 1) {
        $data = $result_->fetch_assoc();
        if ($data['balance'] > 0) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}
