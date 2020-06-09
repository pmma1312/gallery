<?php

include("autoloader.php");

Route::get("/", function() {
    View::html("views/index.html");
});

Route::get("/albums", function() {
    AlbumsController::view();
});

Route::get("/album/([0-9a-zA-Z]+)", function() {
    AlbumController::view();
});

Route::get("/panel", function() {
    View::html("views/panel.html");
}, "Authentication::isAuthenticated");

Route::get("/login", function() {
    View::html("views/login.html");
});

Route::get("/register", function() {
    View::html("views/register.html");
});

Route::get("/images", function() {
    View::html("views/images.html");
});


?>