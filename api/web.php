<?php

include("autoloader.php");

Route::get("/", function() {
    View::html("views/index.html");
});

Route::get("/gallery", function() {
    View::html("views/gallery.html");
});

Route::get("/images", function() {
    View::html("views/images.html");
});

?>