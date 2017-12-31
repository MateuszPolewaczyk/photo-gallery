 <?php $title = 'browser' ?>

 <?php
 ob_start();
 if($_SERVER["REQUEST_METHOD"] == "POST") {
   saveSelectedImages($_POST["img"], '/photo-gallery/index.php/favourites');
 }
 ?>
 <script src="/photo-gallery/js/browser.js"></script>
<div class="search">
  <input type="text" placeholder="Search for images..." onkeyup="browseImages(this.value)">
</div>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>">
  <div style="text-align: center; display: block; width: 1170px; margin: auto; margin-top: 58px;">

      <span id="results"></span>
  </div>
  <div class="foot">
    <input type="submit" name="save" value="Save selected" />
  </div>
</form>
 <?php $content = ob_get_clean() ?>

 <?php include 'layout.php' ?>
