<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

include_once '../../config/Database.php';
include_once '../../class/Order.php';

$database = new Database();
$db = $database->getConnection();

$order = new Order($db);

$data = (array) json_decode(file_get_contents("php://input"));

$requiredFields = array("id", "status");

foreach ($requiredFields as $field) {
    if (empty($data[$field])) {
        print_r(json_encode(array(
            "status" => false,
            "message" => ucfirst($field) . " is required.",
        )));
        exit;
    }
}

$order->id = $data['id'];
$order->status = $data['status'];
$order->modified = date('Y-m-d H:i:s');

if ($order->update()) {

    echo json_encode(array("status" => true, "message" => "Order was updated."));
} else {
    echo json_encode(array("status" => false, "message" => "Unable to create order."));
}
