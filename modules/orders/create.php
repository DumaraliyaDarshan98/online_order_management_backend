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

$requiredFields = array("user_id", "total_amount", "status", "items");

foreach ($requiredFields as $field) {
    if (empty($data[$field])) {
        print_r(json_encode(array(
            "status" => false,
            "message" => ucfirst($field) . " is required.",
        )));
        exit;
    }
}

$order->user_id = $data['user_id'];
$order->total_amount = $data['total_amount'];
$order->status = $data['status'];
$order->instruction = isset($data['instruction']) ? $data['instruction'] : null;
$order->created = date('Y-m-d H:i:s');

if ($order->create()) {
    if (!empty($data['items'])) {
        foreach ($data['items'] as $item) {
            $item = (array) $item;
            $order->item_id = $item['item_id'];
            $order->quantity = $item['quantity'];
            $order->amount = $item['amount'];
            $order->created = date('Y-m-d H:i:s');
            $order->item_create();
        }
    }
    echo json_encode(array("status" => true, "message" => "Order was created."));
} else {
    echo json_encode(array("status" => false, "message" => "Unable to create order."));
}
