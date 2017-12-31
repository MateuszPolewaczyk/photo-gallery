<?php
function registerUser() {
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    createNewUser($_POST["email"], $_POST["login"], $_POST["pass1"], $_POST["pass2"]);
  }
  require 'layout/register.php';
}

function displayHomePage() {
  require 'layout/home.php';
}

function loginPage() {
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    loginUser($_POST["username"], $_POST["pass"]);
  }
  require 'layout/login.php';
}

function browseImages() {
  require 'layout/browser.php';
}

function getResults($q) {
  $a = fetchImages($q);
  require 'layout/results.php';
}

function displayImages() {
  $images = getImages();
  require 'layout/gallery.php';
}

function displayFavourites() {
  $saved = getSavedImages();
  require 'layout/favourites.php';
}

function logout() {
  logoutUser();
}

function uploadImages() {
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST["public"]) || $_SESSION["userId"] == null) {
      $_POST["public"] = true;
    }
    uploadImage($_FILES["fileToUpload"], $_POST["author"], $_POST["title"], $_POST["watermark"], $_POST["public"]);
  }
  require 'layout/upload.php';
}
?>
