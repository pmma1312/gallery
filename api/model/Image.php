<?php

class Image {

    private $conn;
    private $id;
    private $user_id;
    private $file;
    private $path;
    private $uploaded_at;
    private $isOverMaxSize = false;
    private $errors = [];

    public function __construct(array $file, int $user_id) {
        $this->conn = Database::getInstance()->getConn();
        $this->file = $file;
        $this->user_id = $user_id;
    }

    public function validate() : bool {
        $isValid = true;

        if(filesize($this->file['tmp_name']) > 5242880) {
            //array_push($this->errors, "The max size for the file '" . $this->file['name'] . "' is 5MB.");
            //$isValid = false;
            $this->isOverMaxSize = true;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $type = finfo_file($finfo, $this->file['tmp_name']);

        if (isset($type) && !in_array($type, array("image/png", "image/jpg", "image/jpeg", "image/gif"))) {
            array_push($this->errors, "Invalid file type on file '" . $this->file['name'] . "', allowed are png/jpg/gif files.");
            $isValid = false;
        }

        return $isValid;
    }

    public function save() : bool {
        $isSaved = false;

        $basepath = $_SERVER['DOCUMENT_ROOT'] . "/public/img/uploads/";
        $extension = pathinfo($this->file['name'], PATHINFO_EXTENSION);
        $filepath = sha1(microtime()) . "." . $extension;

        if(!is_dir($basepath)) {
            mkdir($basepath);
        }

        if(move_uploaded_file($this->file['tmp_name'], $basepath . $filepath)) {
            $query = sprintf("INSERT INTO image(user_id, path) VALUES(%d, '%s')", $this->user_id, $filepath);

            if($this->conn->query($query)) {
                $this->id = $this->conn->insert_id;
                $this->path = $filepath;
                $this->uploaded_at = date("d.m.Y");

                // Resize the file if it's too big
                if($this->isOverMaxSize) {        
                    $path = $basepath . $filepath;

                    switch($extension) {
                        case "jpeg" || "jpg":
                            $ressource = imagecreatefromjpeg($path);
                            break;
                        case "png":
                            $ressource = imagecreatefrompng($path);
                            break;
                        case "gif":
                            $ressource = imagecreatefromgif($path);
                            break;
                        default:
                            die();
                            break;
                    }
        
                    $ressource = imagescale($ressource, Config::IMAGE_WIDTH);
        
                    switch($extension) {
                        case "jpeg" || "jpg":
                            imagejpeg($ressource, $path);
                            break;
                        case "png":
                            imagepng($ressource, $path);
                            break;
                        case "gif":
                            imagegif($ressource, $path);
                            break;
                        default:
                            die();
                            break;
                    }        
                }

                $isSaved = true;
            }
        }

        return $isSaved;
    }

    public function getErrors() : array {
        return $this->errors;
    }

    public function getPath() : string {
        return $this->path;
    }

    public function getUploadedAt() : string {
        return $this->uploaded_at;
    }

    public function getId() : int {
        return $this->id;
    }

}

?>