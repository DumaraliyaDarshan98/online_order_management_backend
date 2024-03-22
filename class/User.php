<?php
class User
{

    // database connection and table name
    private $conn;
    private $table_name = "users";

    // object properties
    public $id;
    public $role_id;
    public $first_name;
    public $last_name;
    public $email;
    public $phone_no;
    public $password;
    public $created;
    public $limit;
    public $offset;

    // constructor with $db as database connection
    public function __construct($db)
    {
        $this->conn = $db;
    }

    function list()
    {
        $stmt = $this->conn->prepare("SELECT * FROM " . $this->table_name . " LIMIT " . $this->limit . " offset " . $this->offset);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result;
    }

    // signup user
    function signup()
    {

        if ($this->isAlreadyExist()) {
            return false;
        }
        // query to insert record
        $query = "INSERT INTO
                    " . $this->table_name . " (`role_id`,`first_name`,`last_name`,`email`,`phone_no`,`password`,`created`) VALUES (?,?,?,?,?,?,?)";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->role_id = htmlspecialchars(strip_tags($this->role_id));
        $this->first_name = htmlspecialchars(strip_tags($this->first_name));
        $this->last_name = htmlspecialchars(strip_tags($this->last_name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->phone_no = htmlspecialchars(strip_tags($this->phone_no));
        $this->password = htmlspecialchars(strip_tags($this->password));
        $this->created = htmlspecialchars(strip_tags($this->created));

        // bind values
        $stmt->bind_param('issssss', $this->role_id, $this->first_name, $this->last_name, $this->email, $this->phone_no, $this->password, $this->created);

        // execute query
        if ($stmt->execute()) {
            $this->id = $this->conn->insert_id;
            return true;
        }

        return false;
    }
    // login user
    function login()
    {
        // select all query
        $query = "SELECT
                    `id`, `first_name`, `last_name`, `email`, `phone_no`, `password`, `created`, `role_id`
                FROM
                    " . $this->table_name . " 
                WHERE
                    phone_no='" . $this->phone_no . "' AND password='" . $this->password . "' AND role_id='" . $this->role_id . "'";
        // prepare query statement

        $stmt = $this->conn->prepare($query);
        // execute query
        $stmt->execute();
        return $stmt;
    }

    function isAlreadyExist()
    {
        $query = "SELECT *
            FROM
                " . $this->table_name . " 
            WHERE
                (phone_no='" . $this->phone_no . "')";
        // print_r($query);exit;
        // prepare query statement
        $stmt = $this->conn->prepare($query);
        // execute query
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    function loginAdmin()
    {
        // select all query
        $query = "SELECT
                    `id`, `first_name`, `last_name`, `email`, `phone_no`, `password`, `created`, `role_id`
                FROM
                    " . $this->table_name . " 
                WHERE
                email='" . $this->email . "' AND password='" . $this->password . "' AND role_id='" . $this->role_id . "'";
        // prepare query statement

        $stmt = $this->conn->prepare($query);
        // execute query
        $stmt->execute();
        return $stmt;
    }
}
