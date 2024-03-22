<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

// get database connection
include_once '../../config/database.php';

// instantiate user object
include_once '../../class/User.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

$data = (array) json_decode(file_get_contents("php://input"));


$requiredFields = array("first_name", "last_name", "phone_no", "email", "password");

foreach ($requiredFields as $field) {
    if (empty($data[$field])) {
        echo json_encode(array(
            "status" => false,
            "message" => ucfirst($field) . " is required.",
        ));
        exit;
    }
}

if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    echo json_encode(array(
        "status" => false,
        "message" => "Email is invalid",
    ));
    exit;
}

if (!empty($data['phone_no']) && strlen($data['phone_no']) !== 10) {
    echo json_encode(array(
        "status" => false,
        "message" => "Phone no invalid",
    ));
    exit;
}

// set user property values
$user->first_name = $data['first_name'];
$user->role_id = 2;
$user->last_name = $data['last_name'];
$user->phone_no = $data['phone_no'];
$user->email = $data['email'];
$user->password = base64_encode($data['password']);
$user->created = date('Y-m-d H:i:s');

// create the user
if ($user->signup()) {
    $user_arr = array(
        "status" => true,
        "message" => "Successfully Signup!",
        "data" => array(
            "id" => $user->id,
            "username" => $user->first_name,
            "last_name" => $user->last_name,
            "phone_no" => $user->phone_no,
            "email" => $user->email
        )
    );
} else {
    $user_arr = array(
        "status" => false,
        "message" => "Phone number already exists!"
    );
}
echo json_encode($user_arr);
