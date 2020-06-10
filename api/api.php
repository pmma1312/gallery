<?php

include("autoloader.php");

// /api/albums/{limit}/{offset}
Route::get("/api/albums/([0-9]+)/([0-9]+)", function() {
    AlbumsController::loadAlbumsLimit();
});

Route::post("/api/user/create", function() {
    UserController::create();
});

Route::post("/api/auth", function() {
    UserController::login();
});

Route::post("/api/files/upload", function() {
    ImagesController::uploadImages();
}, "Authentication::isAuthenticatedJson");

?>