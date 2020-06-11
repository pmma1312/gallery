<?php

include("autoloader.php");

Route::get("/", function() {
    View::html("views/index.html");
});

Route::get("/albums", function() {
    AlbumsController::view();
});

Route::get("/album/(.*?)+", function() {
    AlbumController::view();
});

Route::get("/panel", function() {
    if(Auth::isAuthorized()) {
        View::html("views/panel.html");
    } else {
        header("Location: /login", 401);
        die();
    }
});

Route::get("/login", function() {
    View::html("views/login.html");
});

Route::get("/logout", function() {
    Auth::logout();
}, "Authentication::isAuthenticated");

Route::get("/register", function() {
    View::html("views/register.html");
});

Route::get("/images", function() {
    View::html("views/images.html");
});


?>