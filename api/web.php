<?php

include("autoloader.php");

Route::get("/", function() {
    View::html("views/index.html");
});

Route::get("/albums", function() {
    AlbumsController::view();
});

Route::get("/images", function() {
    View::html("views/images.html");
});

?>