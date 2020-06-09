<?php

use ReallySimpleJWT\Token;
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

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
            array_push($this->errors, "The min. length for your password is 8 characters!");
            $dataIsValid = false;
        }

        if(strlen($this->password) > 64) {
            array_push($this->errors, "The max. length for your password is 64 characters!");
            $dataIsValid = false;
        }

        if(!preg_match("#[0-9]+#", $this->password ) ) {
            array_push($this->errors, "Password must include at least one number!");
            $dataIsValid = false;
        }

        if(!preg_match("#[A-Z]+#", $this->password ) ) {
            array_push($this->errors, "Password must include at least one uppercase character!");
            $dataIsValid = false;
        }
            
        if(!preg_match("#\W+#", $this->password ) ) {
            array_push($this->errors, "Password must include at least one special character!");
            $dataIsValid = false;
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

    public function verifyPassword() : bool {
        return password_verify($this->password, $this->hash);
    }

    public function exists() : bool {
        return !is_null($this->id);
    }

    public function getErrors() : array {
        return $this->errors;
    }

    public function getToken() : string {
        $payload = [
            'iat' => time(),
            'uid' => $this->id,
            'exp' => time() + (60 * 30), // is valid for 30 min
            'iss' => Config::HOST_NAME
        ];
        
        $token = Token::customPayload($payload, Config::TOKEN_SECRET);

        return $token;
    }

}

?>