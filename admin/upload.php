<?php

$base = 'https://dipantan.online' . '/quizzing/';

$path = '../uploads/';


if (isset($_FILES['file'])) {
    $base_url = $_SERVER['HTTP_HOST'] . '/';
    $file = $_FILES['file'];

    // check if file already exists
    // if (file_exists($path . $file['name'])) {
    //     echo "File already exists.";
    // } else {
    // check if file is an image
    if (getimagesize($file['tmp_name'])) {
        // check if file is too large
        if ($file['size'] > 2097152) {
            echo json_encode(['error' => true, 'message' => 'File is too large.']); // return error message if file is too large
        } else {
            // move file to uploads directory
            $res = move_uploaded_file($file['tmp_name'], $path . $file['name']);
            if ($res) {
                echo json_encode(['error' => false, 'url' => $base . str_replace('../', '', $path) . $file['name']]); // return url of uploaded file
            } else {
                echo json_encode(['error' => true, 'message' => 'File could not be uploaded.']); // return error message if file could not be uploaded
            }
        }
    } else {
        echo json_encode(['error' => true, 'message' => 'File is not an image.']); // return error message if file is not an image
    }
    // }
} else {
    echo json_encode(['error' => true, 'message' => 'No file selected.']); // return error message if no file is selected
}
