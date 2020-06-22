<?php

class Authentication {

    public static function isAuthenticated($next) {

        if(Auth::isAuthorized()) {
            $next();
        } else {
            self::writeLog();
            View::html("views/unauthorized.html");
        }

    }

    public static function isAuthenticatedJson($next) {

        if(Auth::isAuthorized()) {
            $next();
        } else {
            self::writeLog();
            View::json(DefaultHandler::unauthorizedAccess());
        }

    }

    private static function writeLog() : void {
        $logger = Logger::getInstance();
        $logger->write_log("unauthorized access on " . Route::getRequestRoute(), Logger::LOG_LEVEL_WARNING);
    }

}

?>