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

}

?>