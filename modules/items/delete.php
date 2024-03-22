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

if (!empty($data->id)) {
	$items->id = $data->id;
	if ($items->delete()) {
		echo json_encode(array("status" => true, "message" => "Item was deleted."));
	} else {
		echo json_encode(array("status" => false, "message" => "Unable to delete item."));
	}
} else {
	echo json_encode(array("status" => false, "message" => "Unable to delete items. Data is incomplete."));
}
