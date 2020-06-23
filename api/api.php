<?php

include("autoloader.php");

// /api/albums/{limit}/{offset}
Route::get("/api/albums/([0-9]+)/([0-9]+)", function() {
    AlbumsController::loadAlbumsLimit();
});

// /api/images/{limit}/{offset}
Route::get("/api/images/([0-9]+)/([0-9]+)", function() {
    ImagesController::listImagesLimit();
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
    ImageController::deleteImage();
}, "Authentication::isAuthenticatedJson");

// /api/image/visibility/id z.B => /api/image/0/1 => change visibility to public for imgid 1 
Route::put("/api/image/([0-9]+)/([0-9]+)", function() {
    ImageController::changeVisibility();
}, "Authentication::isAuthenticatedJson");

Route::post("/api/album/protect", function() {
    AlbumController::addPassword();
});

Route::post("/api/album/unprotect", function() {
    AlbumController::removePassword();
});

Route::get("/api/album/(.*?)+", function() {
    AlbumController::load();
});

?>