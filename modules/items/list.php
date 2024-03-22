<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

include_once '../../config/Database.php';
include_once '../../class/Items.php';

$database = new Database();
$db = $database->getConnection();

$items = new Items($db);

$data = json_decode(file_get_contents("php://input"));

$page = isset($data->page) ? $data->page : 1;
$items->limit = isset($data->limit) ? $data->limit : 10;
$items->offset = ($page - 1) * $items->limit;

$result = $items->list();

if ($result->num_rows > 0) {

    $item_list = array();
    while ($row = $result->fetch_assoc()) {
        $row['image'] =  BASE_URL . 'uploads/' . $row['image'];
        $item_list[] = $row;
    }

    echo json_encode(array("status" => true, "message" => "Item Found", "data" => $item_list));
} else {
    echo json_encode(
        array("status" => false,"message" => "No item found.")
    );
}
