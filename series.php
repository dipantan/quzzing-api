<?php
include './conn.php';
header("Access-Control-Allow-Origin: *");

$case = $_REQUEST['case'];

switch ($case) {
    case 'get_series':
        $sql = 'select * from series';
        $result = mysqli_query($conn, $sql);
        if ($result) {
            $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
            echo json_encode(["error" => false, "data" => $data]);
        } else {
            $data = array('error' => mysqli_error($conn));
        }
        break;

    case 'get_exams':
        $sql = 'select * from exams';
        $result = mysqli_query($conn, $sql);
        if ($result) {
            $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
            echo json_encode(["error" => false, "data" => $data]);
        } else {
            $data = array('error' => mysqli_error($conn));
        }
        break;

    case 'get_series_by_id':
        $id = $_POST['id'];
        $sql = "select * from series where id='$id'";
        $result = mysqli_query($conn, $sql);
        if ($result) {
            $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
            echo json_encode(["error" => false, "data" => $data]);
        } else {
            $data = array('error' => mysqli_error($conn));
        }
        break;

    case 'update_point':
        $user_id = $_POST['user_id'];
        $amount = $_POST['amount'];
        $status = updatePoint($user_id, $amount);
        if ($status) {
            echo json_encode(["error" => false, "message" => "Point updated successfully"]);
        } else {
            echo json_encode(["error" => true, "message" => "Point update failed"]);
        }
        break;

    default:
        break;
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
