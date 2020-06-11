<?php

include("autoloader.php");

// /api/albums/{limit}/{offset}
Route::get("/api/albums/([0-9]+)/([0-9]+)", function() {
    AlbumsController::loadAlbumsLimit();
});

Route::get("/api/user/images", function() {
    ImagesController::listImagesForUser();
}, "Authentication::isAuthenticatedJson");

// /api/user/images/{limit}/{offset}
Route::get("/api/user/images/([0-9]+)/([0-9]+)", function() {
    ImagesController::listImagesForUserLimit();
}, "Authentication::isAuthenticatedJson");

Route::post("/api/user/create", function() {
    UserController::create();
});

Route::post("/api/auth", function() {
    UserController::login();
});

Route::post("/api/files/upload", function() {
    ImagesController::uploadImages();
}, "Authentication::isAuthenticatedJson");

Route::delete("/api/image/([0-9]+)", function() {
    ImagesController::deleteImage();
}, "Authentication::isAuthenticatedJson");

?>