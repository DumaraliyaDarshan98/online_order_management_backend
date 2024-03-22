<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

include_once '../../config/Database.php';
include_once '../../class/Items.php';

$database = new Database();
$db = $database->getConnection();

$items = new Items($db);

$data = $_POST;

$requiredFields = array("id", "name", "description", "price");

foreach ($requiredFields as $field) {
	if (empty($data[$field])) {
		print_r(json_encode(array(
			"status" => false,
			"message" => ucfirst($field) . " is required.",
		)));
		exit;
	}
}

$fileName = '';
if (isset($_FILES["image"])) {

	$uploadDir = UPLOAD_URL;
	// Create directory if it doesn't exist
	if (!file_exists($uploadDir)) {
		mkdir($uploadDir, 0777, true);
	}

	$fileTmpPath = $_FILES['image']['tmp_name'];
	$fileName = time() . '_' . $_FILES['image']['name'];

	$uploadPath = $uploadDir . $fileName;
	// Move file from temporary location to destination
	move_uploaded_file($fileTmpPath, $uploadPath);
}

if ($fileName != '') {
	$result = $items->detail();
	$detail = $result->fetch_assoc();
	$fileName = $detail['image'];
}

$items->id = $data['id'];
$items->name = $data['name'];
$items->description = $data['description'];
$items->price = $data['price'];
$items->modified = date('Y-m-d H:i:s');

if ($items->update()) {
	echo json_encode(array("status" => true, "message" => "Item was updated."));
} else {
	echo json_encode(array("status" => false, "message" => "Unable to update items."));
}
