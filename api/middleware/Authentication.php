<?php

class Authentication {

    public static function isAuthenticated($next) {

        if(Auth::isAuthorized()) {
            $next();
        } else {
            View::html("views/unauthorized.html");
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