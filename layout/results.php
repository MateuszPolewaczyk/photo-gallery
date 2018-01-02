<?php ob_start();
$i = 0;
foreach ($a as $img): ?>
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
<?= $results = ob_get_clean() ?>
