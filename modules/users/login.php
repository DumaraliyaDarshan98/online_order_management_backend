<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

// include database and object files
include_once '../../config/database.php';
include_once '../../class/User.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

// prepare user object
$user = new User($db);

$data = (array) json_decode(file_get_contents("php://input"));

$requiredFields = array("phone_no", "password");

foreach ($requiredFields as $field) {
    if (empty($data[$field])) {
        echo json_encode(array(
            "status" => false,
            "message" => ucfirst($field) . " is required.",
        ));
        exit;
    }
}

if (!empty($data['phone_no']) && strlen($data['phone_no']) !== 10) {
    echo json_encode(array(
        "status" => false,
        "message" => "Phone no invalid",
    ));
    exit;
}

// set ID property of user to be edited
$user->phone_no = $data['phone_no'];
$user->role_id = 2;
$user->password = base64_encode($data['password']);

// read the details of user to be edited
$stmt = $user->login();
$result = $stmt->get_result();

if ($result->num_rows > 0) {

    $user_detail = $result->fetch_assoc();
    // get retrieved row
    $user_arr = array(
        "status" => true,
        "message" => "Successfully Login!",
        "data" => array(
            "id" => $user_detail['id'],
            "username" => $user_detail['first_name'],
            "last_name" => $user_detail['last_name'],
            "phone_no" => $user_detail['phone_no'],
            "email" => $user_detail['email'],
            "role_id" => $user_detail['role_id']
        )
    );
} else {
    $user_arr = array(
        "status" => false,
        "message" => "Invalid Phone No or Password!",
    );
}
// make it json format
echo json_encode($user_arr);
