<?php
include './conn.php';
header('Content-Type: application/json');
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

$case = $_REQUEST['case'];

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    switch ($case) {
        case 'get_question_by_subject_id':
            $id = $_GET['id'];
            $query = "select * from models where name ='$id'";
            $result = mysqli_query($conn, $query);
            if ($result && mysqli_num_rows($result) > 0) {
                $data = mysqli_fetch_assoc($result);
                $originalData = json_decode($data['data']);
                $formattedData = [];
                foreach ($originalData  as $index => $obj) {
                    $options = [];
                    foreach ($obj as $key => $value) {
                        if (strpos($key, 'Option ') === 0) {
                            $options[] = $value;
                        }
                    }
                    $obj = json_decode(json_encode($obj), true);
                    $correctAnswer = $obj['Correct Answer'];
                    if ($correctAnswer === "1,2,3") {
                        $correctAnswer = implode(',', $options);  // Join options with comma
                    }
                    $formattedData[] = [
                        'id' => $index + 1,
                        'question' => $obj['Question Text'],
                        'options' => $options,
                        'image' => $obj['Image Link'],
                        'time' => $obj['Time in seconds'],
                        'correctAnswer' => $correctAnswer,
                    ];
                }
                echo json_encode(array("error" => false, "data" => $formattedData));
            } else {
                echo json_encode(array("error" => true, "message" => "Question not found"));
            }
            break;

        case 'answer':
            $data = [
                "London",
                "Mars",
                "Pablo Picasso",
                "Blue Whale",
                "F. Scott Fitzgerald",
                "CO2",
                "Netherlands",
                "Liver",
                "Thomas Edison",
                "Tulip",
                "Nitrogen",
                "Alexander Fleming",
                "Mount Fuji",
                "Real",
                "Onion",
                "Platinum",
                "Mars",
                "Fe",
                "Arctic Ocean",
                "8,000 kilometers"
            ];
            echo json_encode(["error" => false, "data" => $data]);
            break;


        default:
            $query = 'select * from subject';
            $result = mysqli_query($conn, $query);
            if ($result) {
                echo json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC));
            }
            break;
    }
} else {
    echo json_encode(['error' => 'Method not allowed']);
}
