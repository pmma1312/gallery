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

/*
 *
 * Auth Routes
 * 
 */

Route::get("/api/user/images", function() {
    ImagesController::listImagesForUser();
}, "Authentication::isAuthenticatedJson");

// /api/user/images/{limit}/{offset}
Route::get("/api/user/images/([0-9]+)/([0-9]+)", function() {
    ImagesController::listImagesForUserLimit();
}, "Authentication::isAuthenticatedJson");

Route::get("/api/user/albums", function() {
    AlbumsController::loadUserAlbums();
}, "Authentication::isAuthenticatedJson");

Route::post("/api/album/create", function() {
    AlbumController::create();
}, "Authentication::isAuthenticatedJson");

Route::post("/api/album/update", function() {
    AlbumController::update();
});

Route::delete("/api/album/([0-9]+)", function() {
    AlbumController::delete();
}, "Authentication::isAuthenticatedJson");

Route::post("/api/files/upload", function() {
    ImagesController::uploadImages();
}, "Authentication::isAuthenticatedJson");

Route::delete("/api/image/([0-9]+)", function() {
    ImagesController::deleteImage();
}, "Authentication::isAuthenticatedJson");

Route::get("/api/album/(.*?)+", function() {
    AlbumController::load();
});

?>