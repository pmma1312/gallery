<?php

class UserController {

    public static function register() {
        $response = DefaultHandler::unableToProccessRequest();

        if(isset($_POST['username']) && isset($_POST['password'])) {
            $user = new User($_POST['username'], $_POST['password']);

            if(!$user->exists()) {
                if($user->isValid()) {
                    if($user->save()) {
                        $response = DefaultHandler::responseOk("Your account has successfully been created!", [
                            "token" => $user->getToken()
                        ]);
                    }
                } else {
                    Controller::setResponseCode(HttpResponseCodes::HTTP_BAD_REQUEST);
                    $response = [
                        "message" => "Some of your data is invalid!",
                        "data" => $user->getErrors()
                    ];
                }
            } else {
                $response = DefaultHandler::badRequest("A user with that username exists already! Please choose another one.");
            }
        } else {
            $response = DefaultHandler::badRequest("Post data missing!");
        }

        View::json($response);
    }

}

?>