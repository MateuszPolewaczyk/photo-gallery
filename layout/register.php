<?php $title = 'register' ?>

<?php ob_start() ?>
  <div class="form-group" style="margin-top: 70px;">
    <span class="form-header">Create an account</span>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>">
      <input value="" type="email" placeholder="E-mail adress" name="email"><br />
      <input maxlength="24" type="text" value="" placeholder="Username" name="login"><br />
      <input value="" type="password" placeholder="Password" name="pass1"><br />
      <input value="" type="password" placeholder="Repeat password" name="pass2"><br />
      <input type="submit" value="Register" name="register">
    </form>
  </div>
<?php $content = ob_get_clean() ?>

<?php include 'layout.php' ?>
