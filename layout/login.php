<?php $title = 'login' ?>

<?php ob_start() ?>
  <div class="form-group" style="margin-top: 70px;">
    <span class="form-header">Login</span>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>">
      <input type="text" value="" placeholder="Username" name="username"><br />
      <input value="" type="password" placeholder="Password" name="pass"><br />
      <input type="submit" value="Login" name="login">
    </form>
  </div>
<?php $content = ob_get_clean() ?>

<?php include 'layout.php' ?>
