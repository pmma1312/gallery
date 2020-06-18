<?php

class ImagesController {

    public static function uploadImages() {
        $response = DefaultHandler::unableToProccessRequest();

        if (isset($_SERVER["CONTENT_LENGTH"])) {
            if ($_SERVER["CONTENT_LENGTH"] < ((int) ini_get('post_max_size') * 1024 * 1024)) {
                if(isset($_FILES['files'])) {
                    $user_id = Auth::getTokenVar("uid");

                    $errors = [];
                    $successful = 0;
            
                    $files = self::restructureFilesArray($_FILES['files']);
            
                    $images = [];
                    
                    // Go through each uploaded file and do the necessary stuff 
                    // to publish it
                    foreach($files as $file) {
                        $image = new Image($file, $user_id);
            
                        if($image->validate()) {
                            if($image->save()) {
                                array_push($images, [
                                    "id" => $image->getId(),
                                    "path" => $image->getPath(),
                                    "uploaded_at" => $image->getUploadedAt()
                                ]);
            
                                $successful++;
                            }
                        } else {
                            array_push($errors, implode("\n", $image->getErrors()));
                        }
                        
                        $response = DefaultHandler::responseOk("Uploaded your files!", [
                            "successful" =>  $successful,
                            "errors" => $errors,
                            "images" => $images
                        ]);
                    }
                } else {
                    $response = DefaultHandler::badRequest("Missing files!");
                }
            } else {
                $response = DefaultHandler::badRequest("The max post size of '" . ((int) ini_get('post_max_size') * 1024 * 1024) . "' has been exceeded!");
            }
        } 

        View::json($response);
    }

    private static function restructureFilesArray(array $files) : array {
        $output = [];

        foreach ($files as $attrName => $valuesArray) {
            foreach ($valuesArray as $key => $value) {
                $output[$key][$attrName] = $value;
            }
        }
        
        return $output;
    }

    public static function listImagesLimit() {
        $routeVars = explode("/", str_replace("/api/images/", "", Route::getRequestRoute()));

        // SQL injection should not be possible because the route only allows numbers
        $limit = $routeVars[0];
        $offset = $routeVars[1];
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

    public static function listImagesForUserLimit() : void {
        $routeVars = explode("/", str_replace("/api/user/images/", "", Route::getRequestRoute()));

        // SQL injection should not be possible because the route only allows numbers
        $limit = $routeVars[0];
        $offset = $routeVars[1];

        if(is_numeric($limit) && is_numeric($offset)) {
            $conn = Database::getInstance()->getConn();
            $query = "SELECT image.id, image.path, DATE_FORMAT(image.uploaded_at, '%d.%m.%Y') AS uploaded_at, image.private FROM image WHERE image.deleted = 0 AND image.user_id = " . Auth::getTokenVar("uid") . " ORDER BY image.id DESC LIMIT $limit OFFSET $offset";
            $result = $conn->query($query);
    
            $data = [];
    
            if($result->num_rows > 0) {
                while(($res = $result->fetch_array(MYSQLI_ASSOC)) != null) {
                    $res['private'] = (bool) $res['private'];
                    array_push($data, $res);
                }
            }

            $response = DefaultHandler::responseOk("Successfully listed your files!", $data);
        } else {
            $response = DefaultHandler::badRequest("Invalid limit or offset!");
        }

        View::json($response);
    }

}

?>