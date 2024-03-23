<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
include '../conn.php';

header("Access-Control-Allow-Origin: *");
$case = $_REQUEST['case'];

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    switch ($case) {
        case 'get':
            $sql = 'select * from user where email is not null and email <> "" order by id desc';
            $result = mysqli_query($conn, $sql);
            if ($result) {
                $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
                echo json_encode($data);
            } else {
                echo mysqli_error($conn);
            }
            break;
        default:
            echo json_encode(array('error' => 'invalid request'));
    }
}
