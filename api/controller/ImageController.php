<?php

class ImageController {

    public static function changeVisibility() {
        $response = DefaultHandler::unableToProccessRequest();
        $conn = Database::getInstance()->getConn();

        $urlVars = str_replace("/api/image/", "", Route::getRequestRoute());
        $urlVars = explode("/", $urlVars);

        // Should be safe from sql injection
        $visibility = $urlVars[0];
        $image_id = $urlVars[1];

        if(is_numeric($visibility) && is_numeric($image_id)) {
            if($visibility == 0 || $visibility == 1) {
                $query = "SELECT user_id FROM image WHERE id = " . $image_id;
                $result = $conn->query($query);

                if($result->num_rows > 0) {
                    $result = $result->fetch_array(MYSQLI_ASSOC);

                    if($result['user_id'] == Auth::getTokenVar("uid")) {
                        $query = "UPDATE image SET private = " . $visibility . " WHERE id = " . $image_id;

                        if($conn->query($query)) {
                            $response = DefaultHandler::responseOk("Successfully changed the visibility for the image");
                        }
                    } else {
                        $response = DefaultHandler::badRequest("You can only change the visibility of your own images");
                    }
                } else {
                    $response = DefaultHandler::badRequest("Invalid image id");
                }
            } else {
                $response = DefaultHandler::badRequest("Invalid visibility, allowed are 0 (public) and 1 (private)");
            }
        } else {
            $response = DefaultHandler::badRequest("Invalid visibility or image id");
        }
        
        View::json($response);
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