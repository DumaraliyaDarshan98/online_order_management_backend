<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

include_once '../../config/Database.php';
include_once '../../class/User.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

$data = json_decode(file_get_contents("php://input"));

$page = isset($data->page) ? $data->page : 1;
$user->limit = isset($data->limit) ? $data->limit : 10;
$user->offset = ($page - 1) * $user->limit;

$result = $user->list();

if ($result->num_rows > 0) {

    $user_list = array();
    while ($row = $result->fetch_assoc()) {
        $user_list[] = $row;
    }
    echo json_encode(array("status" => true, "message" => "Users Found", "data" => $user_list));
} else {
    echo json_encode(
        array("status" => false, "message" => "No user found.")
    );
}
