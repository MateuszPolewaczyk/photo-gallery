<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="/photo-gallery/css/style.css">
    <script type="text/javascript" src="/photo-gallery/js/jquery-3.2.1.min.js"></script>
    <title><?= $title ?></title>
  </head>
  <body>
    <!-- navigation bar -->
    <ul class="nav">
      <li><a id="home" href="/photo-gallery/index.php">Home</a></li>
      <li><a id="gallery" href="/photo-gallery/index.php/gallery">Gallery</a></li>
      <li><a id="browser" href="/photo-gallery/index.php/browser">Search</a></li>
      <li><a id="upload" href="/photo-gallery/index.php/upload">Upload</a></li>
      <?php if($_SESSION["userId"] == null): ?>
        <li style="float: right;"><a id="register" href="/photo-gallery/index.php/register">Register</a></li>
        <li style="float: right;"><a id="login" href="/photo-gallery/index.php/login">Login</a></li>
      <?php elseif ($_SESSION["userId"] != null): ?>
        <?php if(isset($_SESSION["saved"]) && count($_SESSION["saved"]) > 0): ?>
          <li><a id="favourites" href="/photo-gallery/index.php/favourites">Favourites</a></li>
        <?php endif; ?>
        <li style="float: right;"><a id="logout" href="/photo-gallery/index.php/logout">Logout</a></li>
        <li style="float: right;"><a style="background-color: #111;" id="user" href="#"><?= $_SESSION["login"] ?></a></li>
      <?php endif ?>
    </ul>
    <!-- page content -->
    <?= $content ?>
    <script type="text/javascript">
      $(document).ready(function() {
        $('.active').removeClass("active");
        $('#<?= $title ?>').addClass("active");
      });
    </script>
  </body>
</html>
