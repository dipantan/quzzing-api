<?php
include './conn.php';
header('Content-Type: application/json');
error_reporting(E_ALL & ~E_NOTICE);
$case = $_POST['case'];

switch ($case) {
    case 'wallet_balance':
        $mobile = $_POST['mobile'];
        $sqll = "SELECT * FROM wallet WHERE mobile = '$mobile'";
        $result_ = $conn->query($sqll);
        if ($result_->num_rows == 1) {
            $row = $result_->fetch_assoc();
            echo json_encode(array('code' => 'sucess', 'data' => $row));
        } else {
            echo json_encode(array('code' => 'Failed'));
        }
        break;

    case 'deposit':
        $mobile = $_POST['mobile'];
        $amount = $_POST['amount'];
        if (empty($mobile) || empty($amount)) {
            $response = array(
                'success' => false,
                'message' => 'Number not Matched!'
            );
            echo json_encode($response);
            exit;
        }
        $sqll = "UPDATE wallet SET balance = balance + $amount WHERE mobile = '$mobile'";
        $result_ = $conn->query($sqll);
        if ($result_) {
            $sqlll = "INSERT INTO `transaction_history`(`amount`, `status`, `type`, `mobile`, `desc`) VALUES ($amount,'successful','credit',$mobile,'Money added to wallet')";
            $resultt = $conn->query($sqlll);
            if ($resultt) {
                echo json_encode(array('success' => true, 'message' => 'Balance updated'));
            } else {
                echo json_encode(array('success' => false, 'message' => 'Transaction History not updated!'));
            }
        } else {
            $response = array(
                'success' => false,
                'message' => 'Balance update failed!'
            );
            echo json_encode($response);
        }
        break;

    case 'withdraw':
        $mobile = $_POST['mobile'];
        $amount = $_POST['amount'];
        if (empty($mobile) || empty($amount)) {
            $response = array(
                'success' => false,
                'message' => 'Number not Matched!'
            );
            echo json_encode($response);
            exit;
        }
        $sqll = "UPDATE wallet SET balance = balance - $amount WHERE mobile = '$mobile'";
        $result_ = $conn->query($sqll);
        if ($result_) {
            $sqlll = "INSERT INTO `transaction_history`(`amount`, `status`, `type`, `mobile`, `desc`) VALUES ($amount,'successful','debit',$mobile,'Money Transfer to Bank')";
            $resultt = $conn->query($sqlll);
            if ($resultt) {
                echo json_encode(array('success' => true, 'message' => 'Balance updated'));
            } else {
                echo json_encode(array('success' => false, 'message' => 'Transaction History not updated!'));
            }
        } else {
            $response = array(
                'success' => false,
                'message' => 'Balance update failed!'
            );
            echo json_encode($response);
        }
        break;

    case 'history':
        $mobile = $_POST['mobile'];
        $query = "SELECT * FROM `transaction_history` WHERE `mobile` ='$mobile' ORDER BY date DESC";
        $result = mysqli_query($conn, $query);
        if ($result) {
            $transactions = mysqli_fetch_all($result, MYSQLI_ASSOC);

            if (count($transactions) > 0) {
                $response = array(
                    'success' => true,
                    'data' => $transactions
                );
            } else {
                $response = array(
                    'success' => false,
                    'message' => 'No transaction history found for the given user.'
                );
            }
        } else {
            $response = array(
                'success' => false,
                'message' => 'Error fetching transaction history: ' . mysqli_error($conn)
            );
        }
        echo json_encode($response);
        break;

    case 'refer_history':
        $mobile = $_POST['mobile'];
        $query = "SELECT * FROM `referral_history` WHERE `mobile` ='$mobile' ORDER BY date DESC";
        $result = mysqli_query($conn, $query);
        if ($result) {
            $transactions = mysqli_fetch_all($result, MYSQLI_ASSOC);

            if (count($transactions) > 0) {
                $response = array(
                    'success' => true,
                    'data' => $transactions
                );
            } else {
                $response = array(
                    'success' => false,
                    'message' => 'No transaction history found for the given user.'
                );
            }
        } else {
            $response = array(
                'success' => false,
                'message' => 'Error fetching transaction history: ' . mysqli_error($conn)
            );
        }
        echo json_encode($response);
        break;

    case 'point':
        $id = $_POST['id'];
        $sql = "select point from user where mobile='$id'";
        $result = mysqli_query($conn, $sql);
        if ($result) {
            echo json_encode(["error" => false, "data" => mysqli_fetch_assoc($result)]);
        } else {
            echo json_encode(["error" => true, "message" => "Error fetching point"]);
        }
        break;

    default:
        break;
}
