<?php
class Items
{

	private $itemsTable = "items";
	public $id;
	public $name;
	public $description;
	public $price;
	public $image;
	public $created;
	public $modified;
	public $limit;
	public $offset;
	private $conn;

	public function __construct($db)
	{
		$this->conn = $db;
	}

	function detail()
	{
		$stmt = $this->conn->prepare("SELECT * FROM " . $this->itemsTable . " WHERE id = ?");
		$stmt->bind_param("i", $this->id);
		$stmt->execute();
		$result = $stmt->get_result();
		return $result;
	}

	function list()
	{
		$stmt = $this->conn->prepare("SELECT * FROM " . $this->itemsTable . " LIMIT " . $this->limit . " offset " . $this->offset);
		$stmt->execute();
		$result = $stmt->get_result();
		return $result;
	}

	function create()
	{

		$stmt = $this->conn->prepare("
			INSERT INTO " . $this->itemsTable . "(`name`, `description`, `price`, `image`, `created`)
			VALUES(?,?,?,?,?)");

		$this->name = htmlspecialchars(strip_tags($this->name));
		$this->description = htmlspecialchars(strip_tags($this->description));
		$this->price = htmlspecialchars(strip_tags($this->price));
		$this->image = htmlspecialchars(strip_tags($this->image));
		$this->created = htmlspecialchars(strip_tags($this->created));


		$stmt->bind_param("ssiss", $this->name, $this->description, $this->price, $this->image, $this->created);

		if ($stmt->execute()) {
			return true;
		}

		return false;
	}

	function update()
	{

		$stmt = $this->conn->prepare("
			UPDATE " . $this->itemsTable . " 
			SET name= ?, description = ?, price = ?, image = ?, modified = ?
			WHERE id = ?");

		$this->id = htmlspecialchars(strip_tags($this->id));
		$this->name = htmlspecialchars(strip_tags($this->name));
		$this->description = htmlspecialchars(strip_tags($this->description));
		$this->price = htmlspecialchars(strip_tags($this->price));
		$this->image = htmlspecialchars(strip_tags($this->image));
		$this->modified = htmlspecialchars(strip_tags($this->modified));

		$stmt->bind_param("ssissi", $this->name, $this->description, $this->price, $this->image, $this->modified, $this->id);

		if ($stmt->execute()) {
			return true;
		}

		return false;
	}

	function delete()
	{

		$stmt = $this->conn->prepare("
			DELETE FROM " . $this->itemsTable . " 
			WHERE id = ?");

		$this->id = htmlspecialchars(strip_tags($this->id));

		$stmt->bind_param("i", $this->id);

		if ($stmt->execute()) {
			return true;
		}

		return false;
	}
}
