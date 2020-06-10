<?php

class Authentication {

    public static function isAuthenticated($next) {

        if(Auth::isAuthorized()) {
            $next();
        } else {
            header("Location: /login", 401);
            die();
        }

    }

    public static function isAuthenticatedJson($next) {

        if(Auth::isAuthorized()) {
            $next();
        } else {
            View::json(DefaultHandler::unauthorizedAccess());
        }

    }

}

?>