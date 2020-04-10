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

        <?php if (!empty($_POST['email'])): ?>
            $('#password').focus();
        <?php else: ?>
            $('#email').focus();
        <?php endif; ?>
    });
  </script>
</head>
<body>
  <div id="main">
    <?php include 'header2.php'; ?>
    <div id="site_content">
      <h1>Register</h1>
      <div id="content">
        <?php if (isset($statusMessage)): ?>
            <p class="error"><?php echo htmlspecialchars($statusMessage); ?></p>
        <?php endif; ?>
        <form method="post" action="register.php">
          <div class="input_form">
            <div>
              <label>E-mail address:</label>
              <input type="text" id="email" name="email" spellcheck="false" value="<?php if (isset($_POST['email'])) echo htmlspecialchars($_POST['email']); ?>" />
            </div>
            <div>
              <label>Password:</label>
              <input type="password" id="password" name="password" value="" />
            </div>
            <div>
              <label>First name:</label>
              <input type="text" id="first_name" name="first_name" spellcheck="false" value="<?php if (isset($_POST['first_name'])) echo htmlspecialchars($_POST['first_name']); ?>" />
            </div>
            <div>
              <label>Last name:</label>
              <input type="text" id="last_name" name="last_name" spellcheck="false" value="<?php if (isset($_POST['last_name'])) echo htmlspecialchars($_POST['last_name']); ?>" />
            </div>
            <div class="submit_button">
              <input type="submit" id="submitButton" name="submitButton" value="Register" />
              <p align="left"><a href="login.php">Login</a></p>
            </div>
          </div>
        </form>
      </div>
    </div>
    <?php include 'footer.php'; ?>
  </div>
</body>
</html>
