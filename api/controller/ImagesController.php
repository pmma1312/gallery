<?php

class ImagesController {

    public static function uploadImages() {
        $response = DefaultHandler::unableToProccessRequest();

        $user_id = Auth::getTokenVar("uid");

        $errors = [];
        $successful = 0;

        $files = self::restructureFilesArray($_FILES['files']);

        foreach($files as $file) {
            $image = new Image($file, $user_id);

            if($image->validate()) {
                if($image->save()) {
                    $successful++;
                }
            } else {
                array_push($errors, implode("\n", $image->getErrors()));
            }

            $response = DefaultHandler::responseOk("Uploaded your files!", [
                "successful" =>  $successful,
                "errors" => $errors
            ]);
        }

        View::json($response);
    }

    private static function restructureFilesArray(array $files) {
        $output = [];

        foreach ($files as $attrName => $valuesArray) {
            foreach ($valuesArray as $key => $value) {
                $output[$key][$attrName] = $value;
            }
        }
        
        return $output;
    }

    public static function listImagesForUser() {
        $conn = Database::getInstance()->getConn();
        $query = "SELECT image.id, image.path, DATE_FORMAT(image.uploaded_at, '%d.%m.%Y') AS uploaded_at FROM image WHERE image.deleted = 0 AND image.user_id = " . Auth::getTokenVar("uid") . " ORDER BY image.id DESC";
        $result = $conn->query($query);

        $data = [];

        if($result->num_rows > 0) {
            while(($res = $result->fetch_array(MYSQLI_ASSOC)) != null) {
                array_push($data, $res);
            }
        }

        View::json(DefaultHandler::responseOk("Successfully listed your files!", $data));
    }

    public static function deleteImage() {
        $response = DefaultHandler::unableToProccessRequest();

        $conn = Database::getInstance()->getConn();
        $imgid = $conn->real_escape_string(str_replace("/api/image/", "", Route::getRequestRoute()));

        // Check if image is owned by user
        $query = "SELECT id, user_id, path FROM image WHERE id = " . $imgid;
        $result = $conn->query($query);

        if($result->num_rows > 0) {
            $result = $result->fetch_array(MYSQLI_ASSOC);

            if($result['user_id'] == Auth::getTokenVar("uid")) {
                $query = "SELECT thumbnail_id FROM album WHERE deleted = 0 AND thumbnail_id = " . $result['id'];
                $path = $result['path'];

                $result = $conn->query($query);

                if($result->num_rows < 1) {
                    if(file_exists($_SERVER['DOCUMENT_ROOT'] . "/public/img/uploads/" . $path)) {
                        unlink($_SERVER['DOCUMENT_ROOT'] . "/public/img/uploads/" . $path);
                        
                        $query = "UPDATE image SET deleted = 1 WHERE id = " . $imgid;

                        if($conn->query($query)) {
                            $response = DefaultHandler::responseOk("Your image has been deleted!");
                        }
                    }
                } else {
                    $response = DefaultHandler::badRequest("This image can't be deleted, please delete the album that uses it as thumbnail first.");
                }
            } else {
                $response = DefaultHandler::badRequest("You can only delete your own images");
            }
        } else {
            $response = DefaultHandler::badRequest("Invalid image id");
        }   

        View::json($response);
    }

}

?>