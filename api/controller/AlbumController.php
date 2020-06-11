<?php

class AlbumController {

    public static function view() {
        View::html("views/album.html");
    }

    public static function create() {
        $response = DefaultHandler::unableToProccessRequest();

        if(isset($_POST["json"])) {
            $data = json_decode($_POST['json'], true);

            if(isset($data['image_ids']) && isset($data['name']) && isset($data['thumbnail'])) {
                $user_id = Auth::getTokenVar("uid");
                $thumbnail_id = $data['thumbnail'];
                $image_ids = $data['image_ids'];

                $album = new Album($user_id, $data['name'], $thumbnail_id);

                if(!$album->exists()) {
                    if($album->validate()) {
                        if($album->save()) {
                            foreach($image_ids as $image_id) {
                                $album->addImageToAlbum($image_id);
                            }
    
                            $response = DefaultHandler::responseOk("Successfully created the album!", [
                                "id" => $album->getId(),
                                "name" => $album->getName(),
                                "created_at" => $album->getCreatedAt(),
                                "images" => sizeof($image_ids)
                            ]);
                        }
                    } else {
                        Controller::setResponseCode(HttpResponseCodes::HTTP_BAD_REQUEST);
                        $response = [
                            "message" => "Error!",
                            "data" => $album->getErrors()
                        ];
                    }
                } else {
                    $response = DefaultHandler::badRequest("An album with this name exists already!");
                }
            } else {
                $response = DefaultHandler::badRequest("Invalid json data!");
            }
        } else {
            $response = DefaultHandler::badRequest("Post data missing!");
        }

        View::json($response);
    }

    public static function delete() {
        $response = DefaultHandler::unableToProccessRequest();

        $albumId = str_replace("/api/album/", "", Route::getRequestRoute());

        if(is_numeric($albumId)) {
            $album = new Album(null, null, null, $albumId);

            if($album->exists()) {
                if(Auth::getTokenVar("uid") == $album->getUserId()) {
                    if($album->delete()) {
                        $response = DefaultHandler::responseOk("The album has been deleted!");
                    }
                } else {
                    $response = DefaultHandler::badRequest("You can only delete your own albums!");
                }
            } else {
                $response = DefaultHandler::badRequest("The album you're trying to delete doesn't exist!");
            }
        }

        View::json($response);
    }

    public static function load() {
        $conn = Database::getInstance()->getConn();
        $albumName = $conn->real_escape_string(str_replace("/api/album/", "", Route::getRequestRoute()));

        $query = "SELECT image.id, image.path, DATE_FORMAT(image.uploaded_at, '%d.%m.%Y') AS uploaded_at FROM album JOIN image_to_album ON album.id = image_to_album.album_id JOIN image ON image_to_album.image_id = image.id WHERE album.name = '" . $albumName . "'";
        $result = $conn->query($query);

        $data = [];

        if($result->num_rows > 0) {
            $images = [];

            while(($res = $result->fetch_array(MYSQLI_ASSOC)) != null) {
                array_push($images, $res);
            }

            $data = [
                "name" => strip_tags($albumName),
                "images" => $images
            ];
        } else {
            $data = [
                "name" => "Not found!",
                "images" => []
            ];
        }

        View::json(DefaultHandler::responseOk("Successfully got data for album!", $data));
    }

}

?>