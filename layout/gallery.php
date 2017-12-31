<?php $title = 'gallery' ?>

<?php
  ob_start();
  if(isset($_GET["success"])) {
    showSuccessBox('Image uploaded successfully!');
    unset($_GET["success"]);
  }
  $i = 0;
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    saveSelectedImages($_POST["img"], '/photo-gallery/index.php/gallery');
  }
?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>">
  <div style="text-align: center; display: block; width: 1170px; margin: auto;">
    <h1>Gallery</h1>
  <?php foreach ($images as $img): ?>
    <div class="gallery">
      <input type="checkbox" name="img[]" value="<?= $img['_id'] ?>"
      <?php
      if (isset($_SESSION["saved"])) {
        if (in_array($img["_id"], $_SESSION["saved"])) {
          echo "checked";
        }
      }
      ?>>
      <a target="_blank" href="<?= $img["image"]["img_watermark_path"] ?>">
        <img src="<?= $img["image"]["thumb_path"] ?>" alt="<?= $img["image"]["title"] ?>" />
      </a>
      <?php if ($img["public"] == false): ?>
        <br /><strong style="color: #f44336;">Private</strong>
      <?php else: ?>
        <br /><strong style="color: #4CAF50;">Public</strong>
      <?php endif; ?>
      <div class="desc">
        <strong>Title:</strong><br /> <?= $img["image"]["title"] ?><br />
        <strong>Author:</strong><br /> <?= $img["author"]["name"] ?>
      </div>
    </div>
  <?php endforeach ?>
</div>
<div class="foot">
  <input type="submit" name="save" value="Save selected" />
</div>
</form>
<?php $content = ob_get_clean() ?>

<?php include 'layout.php' ?>
