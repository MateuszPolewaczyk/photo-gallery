<?php
// start session
session_start();
if(!isset($_SESSION["userId"]) || !isset($_SESSION["login"])){
  $_SESSION["userId"] = null;
  $_SESSION["login"] = null;
}
// load and initialize any global libraries
require_once 'model.php';
require_once 'controllers.php';

// route the request internally
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

switch ($uri) {
  case '/photo-gallery/index.php/':
    continue;

  case '/photo-gallery/index.php':
    displayHomePage();
    break;

  case '/photo-gallery/index.php/gallery':
    displayImages();
    break;

  case '/photo-gallery/index.php/upload':
    uploadImages();
    break;

  case '/photo-gallery/index.php/register':
    registerUser();
    break;

  case '/photo-gallery/index.php/login':
    loginPage();
    break;

  case '/photo-gallery/index.php/logout':
    logout();
    break;

  case '/photo-gallery/index.php/favourites':
    displayFavourites();
    break;

  case '/photo-gallery/index.php/browser':
    browseImages();
    break;

  case '/photo-gallery/index.php/ajax':
    getResults($_REQUEST["q"]);
    break;

  default:
    header('HTTP/1.1 404 Not Found');
    echo '<html><body><h1>Page Not Found</h1></body></html>';
    break;
}
?>
