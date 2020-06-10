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
        $query = "SELECT image.path, DATE_FORMAT(image.uploaded_at, '%d.%m.%Y') AS uploaded_at FROM image WHERE image.user_id = " . Auth::getTokenVar("uid");
        $result = $conn->query($query);

        $data = [];

        if($result->num_rows > 0) {
            while(($res = $result->fetch_array(MYSQLI_ASSOC)) != null) {
                array_push($data, $res);
            }
        }

        View::json(DefaultHandler::responseOk("Successfully listed your files!", $data));
    }

}

?>