<?php

class Config {

    /*
     * This config class has to be adjusted
     * depending on your application
     */

    # Basic configuration
    const HOST_NAME = "localhost";
    const APP_NAME = "pGallery";
    const APP_VERSION = "1";
    const SESSION_EXPIRES = 15; # The value is in minutes
    const MAX_REQUESTS_SECOND = 1; # The treshold for requests in seconds
    const FORCE_HTTPS = false;
    const TOKEN_SECRET = "D9G+c>/mfR}NaMLtra-rS+S^A=2D'\g";

    # Database configuration
    const DB_HOST = "localhost";
    const DB_DATABASE = "pGallery";
    const DB_USER = "gallery";
    const DB_PASSWORD = "1337";

}

?>