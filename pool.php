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
        case 'get':
            $query = 'SELECT * FROM pool';
            $result = mysqli_query($conn, $query);
            if ($result) {
                $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
                echo json_encode($data);
            } else {
                echo json_encode(['error' => 'Error']);
            }
            break;


        case 'getbysub':
            $sub_id = $_GET['sub_id'];
            $query = "SELECT * FROM pool WHERE sub_id='$sub_id'";
            $result = mysqli_query(
                $conn,
                $query
            );
            if ($result) {
                $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
                echo json_encode(["error" => false, "data" => $data]);
            } else {
                echo json_encode(["error" => true, "message" => "No pool found"]);
            }
            break;


        case 'getbyuser':
            $user = $_GET['user'];
            $query = "SELECT * FROM pool WHERE find_in_set('$user',user_list)";
            $result = mysqli_query(
                $conn,
                $query
            );
            if (mysqli_num_rows($result) == 0) {
                echo json_encode(["error" => true, "message" => "No pool found"]);
                exit;
            }
            if ($result) {
                $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
                echo json_encode($data);
            } else {
                echo json_encode(["error" => true, "message" => "No pool found"]);
            }
            break;


        case 'postuser':
            $id = $_POST['id'];
            $user_id = $_POST['mobile'];
            $amount = $_POST['amount'];

            // check if amount is less than wallet balance
            if (!checkWalletBalance($user_id, $amount)) {
                echo json_encode(["error" => true, "message" => "Insufficient balance,\nPlease Recharge your wallet."]);
                exit;
            }


            // check pool size
            if (!checkPoolSize($id)) {
                echo json_encode(["error" => true, "message" => "Pool is full"]);
                exit;
            }

            // update user list
            $sql = "update pool set user_list=if(find_in_set('$user_id',user_list),user_list,concat(user_list,'$user_id,')) where id='$id'";
            $result = mysqli_query(
                $conn,
                $sql
            );

            if (mysqli_affected_rows($conn) > 0) {
                if ($result) {
                    // update available seats
                    if ($update = updateAvailableSeats($id) && $wupdate = walletUpdate($user_id, $amount) && $pupdate = updatePoint($user_id, 25)) {
                        echo json_encode(["error" => false, "message" => "Pool create success"]);
                    } else {
                        echo json_encode(["error" => true, "message" => "Something went wrong"]);
                    }
                } else {
                    echo json_encode(["error" => true, "message" => "Pool create failed"]);
                }
            } else {
                echo json_encode(["error" => true, "message" => "User already joined"]);
            }
            break;

        case 'post':
            $subject = $_POST['subject'];

            $total_seats = $_POST['total_seats'];

            $start_date = $_POST['start_date'];

            $end_date = $_POST['end_date'];

            $entry_fee = $_POST['entry_fee'];

            $pool_prize = $_POST['pool_prize'];

            $model = $_POST['model'];

            $sql = "INSERT INTO `pool`(
                        `sub_id`,
                        `model_id`,
                        `pool_prize`,
                        `total_seats`,
                        `start_date`,
                        `end_date`,
                        `entry_fee`,
                        `zone`
                    )
                    VALUES(
                        '$subject',
                        '$model',
                        '$pool_prize',
                        '$total_seats',
                        '$start_date',
                        '$end_date',
                        '$entry_fee',
                        'zone'
                    )";
            $result = mysqli_query($conn, $sql);
            if ($result) {
                echo json_encode(["status" => "Pool create success"]);
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

function checkWalletBalance($mobile, $amount)
{
    global $conn;
    $sqll = "SELECT * FROM wallet WHERE mobile = '$mobile'";
    $result_ = $conn->query($sqll);
    if ($result_->num_rows == 1) {
        $data = $result_->fetch_assoc();
        if ($data['balance'] > 0 && $data['balance'] >= $amount) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function updatePoint($user_id, $amount)
{
    global $conn;
    $status = true;
    $sql = "update user set point=point+$amount where mobile='$user_id'";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        $status = true;
    } else {
        $status = false;
    }
    return $status;
}
