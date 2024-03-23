<?php
include './conn.php';
header("Access-Control-Allow-Origin: *");

$case = $_REQUEST['case'];

if ($_SERVER['REQUEST_METHOD'] == "GET" && isset($case)) {
    switch ($case) {
        case 'fetch':
            $query = 'SELECT * FROM subject';
            $result = mysqli_query($conn, $query);
            if ($result) {
                echo json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC));
            }
            break;
        case 'findbysub':
            $id = $_REQUEST['id'];
            $query = "SELECT id,name,image,date FROM subject WHERE FIND_IN_SET($id, state) > 0";
            $result = mysqli_query($conn, $query);
            if ($result) {
                echo json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC));
            }
            break;
        default:
            break;
    }
} else {
    echo json_encode(['error' => 'Method not allowed']);
}
