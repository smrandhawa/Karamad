<!doctype html>
<html>
<head>
  <title>Home</title>
  <?php include 'stylesheets.php'; ?>
  <?php include 'scripts.php'; ?>
  <script type="text/javascript">
    $(function()
    {
        $('#submitButton').button();

        <?php if (!empty($_POST['npassword'])): ?>
            $('#npassword').focus();
        <?php else: ?>
            $('#cpassword').focus();
        <?php endif; ?>
    });
  </script>
</head>
<body>
  <div id="main">
    <?php include 'header3.php'; ?>
    <div id="site_content">
      <h1>Reset your Password</h1>
        <?php if (isset($statusMessage)): ?>
            <p class="error"><?php echo htmlspecialchars($statusMessage); ?></p>
        <?php endif; ?>
        <form method="post" action="reactivate.php">
          <div class="input_form">
            <div>
              <label>New Password : </label>
              <input type="password" id="npassword" name="npassword" value="" />
            </div>
            <div>
              <label>Confirm Password:</label>
              <input type="password" id="cpassword" name="cpassword" value="" />
            </div>
            <div style="display: none">
              <input type="text" name="ac" value="<?php if (isset($ac)) echo htmlspecialchars($ac); ?>" />
            </div>
            <div class="submit_button">
              <input  type="submit" id="submitButton" name="submitButton" value="Reactivate" />
            </div>

          </div>
        </form>
    </div>
    <?php include 'footer.php'; ?>
  </div>
</body>
</html>
