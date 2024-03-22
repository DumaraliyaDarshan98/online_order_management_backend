<?php
class Order
{

    // database connection and table name
    private $conn;
    private $order_table = "orders";
    private $order_item_table = "order_items";
    private $item_table = "items";

    // object properties
    public $id;
    public $item_id;
    public $user_id;
    public $total_amount;
    public $instruction;
    public $status;
    public $quantity;
    public $amount;
    public $limit;
    public $offset;
    public $created;
    public $modified;

    // constructor with $db as database connection
    public function __construct($db)
    {
        $this->conn = $db;
    }

    function list()
    {
        if ($this->user_id != 0) {
            $stmt = $this->conn->prepare("SELECT * FROM " . $this->order_table . " WHERE user_id = " . $this->user_id . " LIMIT " . $this->limit . " offset " . $this->offset);
        } else {
            $stmt = $this->conn->prepare("SELECT * FROM " . $this->order_table . " LIMIT " . $this->limit . " offset " . $this->offset);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }


    function order_item_list()
    {
        $stmt = $this->conn->prepare("SELECT * FROM " . $this->order_item_table . " WHERE order_id = " . $this->id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    function item_detail()
    {
        $stmt = $this->conn->prepare("SELECT * FROM " . $this->item_table . " WHERE id = " . $this->item_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    function create()
    {

        // sanitize
        $this->user_id = $this->user_id;
        $this->total_amount = $this->total_amount;
        $this->instruction = $this->instruction;
        $this->status = $this->status;
        $this->created = $this->created;

        // query to insert record
        $query = "INSERT INTO
                    " . $this->order_table . " (`user_id`,`total_amount`,`instruction`,`status`,`created`) VALUES (?,?,?,?,?)";

        // prepare query
        $stmt = $this->conn->prepare($query);


        // bind values
        $stmt->bind_param('idsis', $this->user_id, $this->total_amount, $this->instruction, $this->status, $this->created);
        // execute query
        if ($stmt->execute()) {
            $this->id = $this->conn->insert_id;
            return true;
        }

        return false;
    }

    function item_create()
    {

        // query to insert record
        $query = "INSERT INTO
                    " . $this->order_item_table . " (`order_id`,`item_id`,`quantity`,`amount`,`created`) VALUES (?,?,?,?,?)";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->item_id = htmlspecialchars(strip_tags($this->item_id));
        $this->quantity = htmlspecialchars(strip_tags($this->quantity));
        $this->amount = htmlspecialchars(strip_tags($this->amount));
        $this->created = htmlspecialchars(strip_tags($this->created));

        // bind values
        $stmt->bind_param('sssss', $this->id, $this->item_id, $this->quantity, $this->amount, $this->created);

        // execute query
        if ($stmt->execute()) {
            $this->id = $this->conn->insert_id;
            return true;
        }

        return false;
    }


    function update()
    {

        $stmt = $this->conn->prepare("
			UPDATE " . $this->order_table . " 
			SET status = ?, modified = ?
			WHERE id = ?");

        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->status = htmlspecialchars(strip_tags($this->status));
        $this->modified = htmlspecialchars(strip_tags($this->modified));

        $stmt->bind_param("isi", $this->status, $this->modified, $this->id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}
