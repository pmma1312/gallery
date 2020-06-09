<?php

include("autoloader.php");

// /api/albums/{limit}/{offset}
Route::get("/api/albums/([0-9]+)/([0-9]+)", function() {
    AlbumsController::loadAlbumsLimit();
});

Route::get("/api/middleware/test", function() {
    View::json(DefaultHandler::responseOk("Middleware is working"));
}, "Test::exampleMiddleware");

?>