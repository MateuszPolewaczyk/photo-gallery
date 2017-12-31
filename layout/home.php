<?php $title = 'home' ?>

<?php
  ob_start();
  if(isset($_GET["success"])) {
    showSuccessBox('Logged in successfully!');
    unset($_GET["success"]);
  }
?>
<div>
  <h1>This is just a placeholder ;) You can put any content you like over here</h1>
</div>
<?php $content = ob_get_clean() ?>

<?php include 'layout.php' ?>
