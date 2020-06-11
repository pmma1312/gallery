<?php

class ImageToAlbum {

    private $conn;
    private $album_id;
    private $image_id;
    private $user_id;

    public function __construct($album_id, $image_id) {
        $this->conn = Database::getInstance()->getConn();
        $this->album_id = $album_id;
        $this->image_id = $this->conn->real_escape_string($image_id);
    }

    public function save() : bool {
        $isSaved = false;

        $query = sprintf("INSERT INTO image_to_album(album_id, image_id) VALUES(%d, %d)", $this->album_id, $this->image_id);

        if($this->conn->query($query)) {
            $isSaved = true;
        }

        return $isSaved;
    }

}

?>