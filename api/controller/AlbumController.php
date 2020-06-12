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
        $albumName = urldecode($conn->real_escape_string(str_replace("/api/album/", "", Route::getRequestRoute())));

        // Get album information
        $query = "SELECT album.id, album.thumbnail_id, album.name, album.created_at FROM album WHERE album.name = '" . $albumName . "'";
        $result = $conn->query($query);

        if($result->num_rows > 0) {
            $result = $result->fetch_array(MYSQLI_ASSOC);
            $album = $result;

            // Get images for album
            $query = "SELECT image.id, image.path, DATE_FORMAT(image.uploaded_at, '%d.%m.%Y') AS uploaded_at FROM album LEFT JOIN image_to_album ON album.id = image_to_album.album_id LEFT JOIN image ON image_to_album.image_id = image.id WHERE image_to_album.album_id = " . $album['id'] . " AND album.deleted = 0 AND (image.deleted = 0 OR image.deleted IS NULL)";
            $result = $conn->query($query);

            $data = [];
            $images = [];

            if($result->num_rows > 0) {

                while(($res = $result->fetch_array(MYSQLI_ASSOC)) != null) {
                    if(!is_null($res['id']))
                        array_push($images, $res);
                }

            }

            $data = [
                "album" => $album,
                "images" => $images
            ];
        } else {
            $data = [
                "album" => [
                    "id" => 0,
                    "name" => "Not found!",
                    "thumbnail_id" => 0,
                    "created_at" =>  "01-01-01 01:01:01"
                ],
                "images" => []
            ];
        }

        View::json(DefaultHandler::responseOk("Successfully got data for album!", $data));
    }

    public static function update() {
        $response = DefaultHandler::unableToProccessRequest();

        // TODO: FIX BUG WHERE UPDATE CAN CAUSE MULTIPLE ALBUMS TO HAVE THE SAME NAME (MAYBE FIXED???)
        if(isset($_POST['json'])) {
            $data = json_decode($_POST['json'], true);

            if(isset($data['image_ids']) && isset($data['name']) && isset($data['album_id'])) {
                $user_id = Auth::getTokenVar("uid");
                $album_id = $data['album_id'];

                $checkDuplicateAlbum = new Album($user_id, $data['name'], null, null);

                $album = new Album(null, null, null, $album_id);

                if($checkDuplicateAlbum->getName() == $album->getName() || ($checkDuplicateAlbum->getId() == $album->getId() || !$checkDuplicateAlbum->exists())) {
                    if($album->exists()) {
                        if($album->getUserId() == $user_id) {
                            if($album->update($data)) {
                                $response = DefaultHandler::responseOk("Successfully updated the album!");
                            }
                        } else {
                            $response = DefaultHandler::badRequest("You can only edit your own albums.");
                        }
                    } else {
                        $response = DefaultHandler::badRequest("Invalid album id.");
                    }
                } else {
                    $response = DefaultHandler::badRequest("An album with this name exists already!");
                }
            } else {
                $response = DefaultHandler::badRequest("Missing data.");
            }
        } else {
            $response = DefaultHandler::badRequest("Missing post data.");
        }

        View::json($response);
    }

}

?>