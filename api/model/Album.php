<?php

class Album {

    private $id;
    private $conn;
    private $user_id;
    private $thumbnail_id;
    private $name;
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
        $query = "SELECT * FROM album WHERE ";

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
        } else {
            $this->id = null;
        }
    }

    public function delete() : bool {
        $isDeleted = false;

        $query = "DELETE FROM image_to_album WHERE album_id = " . $this->id;

        $this->conn->query($query);

        $query = "UPDATE album SET deleted = 1 WHERE id = " . $this->id;

        if($this->conn->query($query)) {
            $isDeleted = true;
        }

        return $isDeleted;
    }

    public function exists() : bool {
        return !is_null($this->id);
    }

    public function validate() : bool {
        $isValid = true;

        if(strlen($this->name) < 1) {
            $isValid = false;
            array_push($this->errors, "Your album name has to be atleast 1 character long!");
        }

        if(strlen($this->name) > 24) {
            $isValid = false;
            array_push($this->errors, "Your album name has to be atleast 1 character long!");
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
            $query = "DELETE FROM image_to_album WHERE image_to_album.album_id = " . $this->id;

            $this->conn->query($query);

            foreach($data['image_ids'] as $image_id) {
                $this->addImageToAlbum($image_id);
            }

            $isUpdated = true;
        }

        return $isUpdated;
    }

    public function addImageToAlbum($image_id) : void {
        $imageToAlbum = new ImageToAlbum($this->id, $image_id);
        $imageToAlbum->save();
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