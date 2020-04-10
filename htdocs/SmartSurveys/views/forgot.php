<!doctype html>
<html>
<head>
  <title>Survey Builder Register</title>
  <?php include 'stylesheets.php'; ?>
  <?php include 'scripts.php'; ?>
  <script type="text/javascript">
    $(function()
    {
        $('#submitButton').button();

        <?php if (empty($_POST['email'])): ?>
            $('#email').focus();
        <?php endif; ?>
    });
  </script>
</head>
<body>
  <div id="main">
    <?php include 'header3.php'; ?>
    <div id="site_content">
      <h1>Forgot Password</h1>
      <div id="content">
        <?php if (isset($statusMessage)): ?>
            <p class="error"><?php echo htmlspecialchars($statusMessage); ?></p>
        <?php endif; ?>
        <form method="post" action="forgot.php">
          <div class="input_form">
            <div>
              <label style="width:auto">Enter your E-mail address : </label>
              <input type="text" id="email" name="email" spellcheck="false" value="<?php if (isset($_POST['email'])) echo htmlspecialchars($_POST['email']); ?>" />
            </div>
            <div class="submit_button">
              <input type="submit" id="submitButton" name="submitButton" value="Forgot" />
              <p align="left"><a href="register.php">Register</a></p>
            </div>
          </div>
        </form>
      </div>
    </div>
    <?php include 'footer.php'; ?>
  </div>
</body>
</html>
