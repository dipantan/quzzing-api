<?php
include '../conn.php';
header("Access-Control-Allow-Origin: *");

$case = $_POST['case'];

switch ($case) {
    case 'get':
        $sql = 'SELECT * FROM models';
        $result = $conn->query($sql);
        $data = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        echo json_encode($data);
        break;
    case 'post':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $pattern = '/(?<!\\)(["\'])($|\1)/';
            $replacement = '\\$1';

            $name = $_POST['name'];
            $data = preg_quote($_POST['data'], $pattern);

            $sql = "INSERT INTO models (name, data) VALUES ('$name', '$data')";

            if ($conn->query($sql) === TRUE) {
                echo "New record created successfully";
            } else {
                echo $data;
            }
        } else {
            echo "Method not allowed";
        }
        break;
    case 'put':
        break;
    case 'delete':
        break;
    default:
        echo "Case not found";
        break;
}
