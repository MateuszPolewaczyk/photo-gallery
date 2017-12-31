<?php $title = 'upload' ?>

<?php ob_start() ?>
  <div class="form-group" style="margin-top: 70px;">
    <span class="form-header">Upload image</span>
    <form method="post" enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>">
      <input maxlength="24" value="" type="text" placeholder="Enter image's title" name="title"><br />
      <input maxlength="24" type="text" value="<?= $_SESSION['login'] ?>" placeholder="Author of image" name="author"><br />
      <input value="" type="file" name="fileToUpload"><br />
      <input maxlength="32" value="" type="text" placeholder="Enter watermark text" name="watermark"><br />
      <?php if($_SESSION["userId"] != null): ?>
      <input id="public" type="radio" name="public" value="true" checked>
      <label for="public">Public</label>
      <input id="private" type="radio" name="public" value="false">
      <label for="private">Private</label>
      <?php endif ?>
      <input type="submit" value="Upload" name="upload">
    </form>
  </div>
<?php $content = ob_get_clean() ?>

<?php include 'layout.php' ?>
