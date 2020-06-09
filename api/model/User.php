<?php

class User {

    private $id;
    private $username;
    private $password;
    private $hash;
    private $errors = [];
    private $conn;

    public function __construct(string $username, string $password, $id = null) {
        $this->conn = Database::getInstance()->getConn();
        $this->username = $this->conn->real_escape_string(strip_tags(str_replace(" ", "", $username)));
        $this->password = $password;
        $this->id = $id;

        $this->load();
    }

    private function load() : void {
        $query = "SELECT * FROM user WHERE ";

        if(!$this->exists()) {
            $query .= "username = '" . $this->username . "'";
        } else {
            $query .= "id = " . $this->id;
        }

        $result = $this->conn->query($query);

        if($result->num_rows > 0) {
            $result = $result->fetch_array(MYSQLI_ASSOC);
            $this->id = $result['id'];
            $this->hash = $result['password'];
        }
    }

    public function isValid() : bool {
        $isValid = true;

        if(strlen($this->username) < 1) {
            $isValid = false;
            array_push($this->error, "Your username has to be atleast 1 character long.");
        }

        if(strlen($this->username) > 16) {
            $isValid = false;
            array_push($this->error, "Your username can't be longer than 16 characters.");
        }

        if(strlen($this->password) < 8) {
            $isValid = false;
            array_push($this->error, "Your password has to be atleast 8 characters long.");
        }

        if(strlen($this->password) > 64) {
            $isValid = false;
            array_push($this->error, "Your password can't be longer than 64 characters.");
        }

        return $isValid;
    }

    public function save() : bool {
        $isSaved = false;

        if(!$this->exists()) {
            $password = password_hash($this->password, PASSWORD_ARGON2I);
            $query = sprintf("INSERT INTO user(username, password) VALUES('%s', '%s')", $this->username, $password);
        } else {
            // TODO: IMPLEMENT UPDATE
            $query = "";
        }

        if($this->conn->query($query)) {
            $isSaved = true;
        }

        if(!$this->exists()) {
            $this->id = $this->conn->insert_id;
        }

        return $isSaved;
    }

    public function exists() : bool {
        return !is_null($this->id);
    }

    public function getErrors() : array {
        return $this->errors;
    }

    public function getToken() : string {
        return "HSHFHFHFHFH";
    }

}

?>