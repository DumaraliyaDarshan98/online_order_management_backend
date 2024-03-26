<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

include_once '../../config/Database.php';
include_once '../../class/Order.php';

$database = new Database();
$db = $database->getConnection();

$order = new Order($db);

$data = json_decode(file_get_contents("php://input"));


$page = isset($data->page) ? $data->page : 1;
$order->limit = isset($data->limit) ? $data->limit : 10;
$order->offset = ($page - 1) * $order->limit;
$page = isset($data->page) ? $data->page : 1;

$order->user_id = isset($data->user_id) ?  $data->user_id : 0;

$result = $order->list();

if ($result->num_rows > 0) {

    $order_list = array();
    while ($row = $result->fetch_assoc()) {
        $order_detail = $row;

        $order->id = $row['id'];
        $order_item_result = $order->order_item_list();
        while ($order_item_row = $order_item_result->fetch_assoc()) {
            $order->item_id = $order_item_row['item_id'];
            $item_result = $order->item_detail();
            $order_item_row['item_detail'] = $item_result->fetch_assoc();
			// $order_item_row['item_detail']['image'] = $order_item_row['item_detail']['image'];
            $order_detail['items'][] = $order_item_row;
        }

        $order_list[] = $order_detail;
    }

    echo json_encode(array("status" => true, "message" => "Item Found", "data" => $order_list));
} else {
    echo json_encode(
        array("status" => false, "message" => "No item found.")
    );
}
