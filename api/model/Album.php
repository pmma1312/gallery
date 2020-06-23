<?php

class Album {

    private $id;
    private $conn;
    private $user_id;
    private $thumbnail_id;
    private $name;
    private $password;
    private $errors = [];

    public function __construct($user_id, $name, $thumbnail_id, $id = null) {
        $this->conn = Database::getInstance()->getConn();
        $this->user_id = $user_id;
        $this->thumbnail_id = $thumbnail_id;
        $this->name = $this->conn->real_escape_string(strip_tags(trim($name)));
        $this->id = is_null($id) ? $id : $this->conn->real_escape_string($id);
        $this->load();
    }

    private function load() : void {
        $query = "SELECT * FROM album WHERE deleted = 0 AND ";

        if(!$this->exists()) {
            $query .= "name = '" . $this->name . "'";
        } else {
            $query .= "id = " . $this->id;
        }

        $result = $this->conn->query($query);

        if($result->num_rows > 0) {
            $result = $result->fetch_array(MYSQLI_ASSOC);
            $this->id = $result['id'];
            $this->user_id = $result['user_id'];
            $this->name = $result['name'];
            $this->password = $result['password'];
        } else {
            $this->id = null;
        }
    }

    public function delete() : bool {
        $isDeleted = false;

        $this->deleteImagesFromAlbum();

        $query = "UPDATE album SET deleted = 1 WHERE id = " . $this->id;

        if($this->conn->query($query)) {
            $isDeleted = true;
        }

        return $isDeleted;
    }

    public function exists() : bool {
        return !is_null($this->id);
    }

    public function validate($data = null) : bool {
        $isValid = true;

        if(is_null($data)) {
            $name = $this->name;
        } else {
            $name = $data['name'];
        }

        if(strlen($name) < 1) {
            $isValid = false;
            array_push($this->errors, "Your album name has to be atleast 1 character long!");
        }

        if(strlen($name) > 24) {
            $isValid = false;
            array_push($this->errors, "Your album name can't be longer than 24 characters!");
        }


        return $isValid;
    }

    public function save() : bool {
        $isSaved = false;

        $query = sprintf("INSERT INTO album(user_id, name, thumbnail_id) VALUES(%d, '%s', %d)", $this->user_id, $this->name, $this->thumbnail_id);

        if($this->conn->query($query)) {
            $isSaved = true;
            $this->id = $this->conn->insert_id;
        }

        return $isSaved;
    }

    public function update($data) : bool {
        $isUpdated = false;

        $rows = [];

        if(!is_null($data['thumbnail'])) {
            array_push($rows, "thumbnail_id = " . $data['thumbnail']);
        }

        array_push($rows, "name = '" . $data['name'] . "'");

        $query = sprintf("UPDATE album SET %s WHERE id = %d", implode(",", $rows), $this->id);

        if($this->conn->query($query)) {
            $this->deleteImagesFromAlbum();

            foreach($data['image_ids'] as $image_id) {
                $this->addImageToAlbum($image_id);
            }

            $isUpdated = true;
        }

        return $isUpdated;
    }

    private function deleteImagesFromAlbum() : void {
        $query = "DELETE FROM image_to_album WHERE album_id = " . $this->id;
        $this->conn->query($query);
    }

    public function addImageToAlbum($image_id) : void {
        $imageToAlbum = new ImageToAlbum($this->id, $image_id);
        $imageToAlbum->save();
    }

    public function savePassword() : bool {
        $isSaved = false;

        if($this->password == "NULL") {
            $query = "UPDATE album SET password = NULL WHERE id = " . $this->id;
        } else {
            $query = "UPDATE album SET password = '" . $this->password . "' WHERE id = " . $this->id; 
        }

        if($this->conn->query($query)) {
            $isSaved = true;
        }

        return $isSaved;
    }

    public function setPassword($password) : void {
        if(is_null($password)) {
            $this->password = "NULL";
        } else {
            $this->password = password_hash($password, PASSWORD_ARGON2I);
        }
    }

    public function getId() {
        return $this->id;
    }

    public function getCreatedAt() : string {
        return date("d.m.Y");
    }

    public function getName() : string {
        return $this->name;
    }

    public function getUserId() : int {
        return $this->user_id;
    }

    public function getErrors() : array {
        return $this->errors;
    }

}

?>