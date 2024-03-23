<?php
include '../conn.php';
header("Access-Control-Allow-Origin: *");

$case = $_POST['case'];

switch ($case) {
    case 'fetchbyid':
        $id = $_POST['id'];
        $sql = "select * from model where id ='$id'";
        $result = $conn->query($sql);
        $data = array();
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode($data);
        break;
    case 'register':

        break;
    default:
        break;
}
