<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

include_once '../../config/Database.php';
include_once '../../class/Items.php';

$database = new Database();
$db = $database->getConnection();

$items = new Items($db);


$data = $_GET;

if (empty($data['id'])) {
    print_r(json_encode(array(
        "status" => false,
        "message" => "Item id required",
    )));
    exit;
}
$items->id = $data['id'];

$result = $items->detail();

if ($result->num_rows > 0) {
    $item = $result->fetch_assoc();
    $item['image'] =  BASE_URL . 'uploads/' . $item['image'];

    echo json_encode(array("status" => true, "message" => "Item Found", "data" => $item));
} else {
    echo json_encode(
        array("status" => false,"message" => "No item found.")
    );
}
