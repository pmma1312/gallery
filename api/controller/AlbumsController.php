<?php

class AlbumsController {

    public static function view() {
        View::html("views/albums.html");
    }

    public static function loadAlbumsLimit() {
        $response = DefaultHandler::unableToProccessRequest();
        
        $routeVars = explode("/", str_replace("/api/albums/", "", Route::getRequestRoute()));

        $limit = $routeVars[0];
        $offset = $routeVars[1];

        if(is_numeric($limit) && is_numeric($offset)) {
            $conn = Database::getInstance()->getConn();

            $query = "SELECT album.name, DATE_FORMAT(album.created_at, '%d.%m.%Y') AS created_at, user.username, image.path AS thumbnail FROM album JOIN user ON album.user_id = user.id JOIN image ON album.thumbnail_id = image.id WHERE album.deleted = 0 LIMIT " . $limit . " OFFSET " . $offset;

            $result = $conn->query($query);

            $data = [];

            if($result->num_rows > 0) {
                while(($res = $result->fetch_array(MYSQLI_ASSOC)) != null) {
                    array_push($data, $res);
                }
            }

            $response = DefaultHandler::responseOk("Successfully listed all albums!", $data);
        } else {
            $response = DefaultHandler::badRequest("Invalid album limit or offset!");
        }

        View::json($response);
    }

    public static function deleteAlbum() {
        $conn = Database::getInstance()->getConn();
    }

    public static function loadUserAlbums() {
        $conn = Database::getInstance()->getConn();
        $query = "SELECT album.id, album.name, DATE_FORMAT(album.created_at, '%d.%m.%Y') AS created_at, COUNT(image_to_album.image_id) AS images FROM album JOIN image_to_album ON album.id = image_to_album.album_id WHERE album.deleted = 0 AND album.user_id = " . Auth::getTokenVar("uid") . " GROUP BY image_to_album.album_id";
        $result = $conn->query($query);

        $data = [];

        if($result->num_rows > 0) {
            while(($res = $result->fetch_array(MYSQLI_ASSOC)) != null) {
                array_push($data, $res);
            }
        }

        $response = DefaultHandler::responseOk("Successfully loaded your albums!", $data);

        View::json($response);
    }

}

?>