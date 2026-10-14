<?php
// forgotpass.php
?>

<div class="login-form-card" style="max-width: 100%; box-shadow: none; padding: 0;">
  <h3>CREATE NEW PASSWORD</h3>
  <p style="text-align: center; margin-bottom: 20px; font-size: 14px; color: #666;">Enter your email to reset your password.</p>

  <form method="POST"
    action="sendOtp.php"
    onsubmit="return validateForm();">

    <div class="form-group">
      <label>Email</label>
      <input
      name="Inputemail"
      type="email">    
    </div>
    <div class="form-group">
      <label>New Password</label>
      <input class="form-control" name="InputPassword" type="password" placeholder="Type Your password" autocomplete="off" id="InputPassword" required>
      <span id="password-error1" style="color: red; font-size: 12px;"></span>
<div class="form-group">
    <label>Confirm Password</label>
    <input
    class="form-control"
    type="password"
    id="ConfirmPassword"
    name="ConfirmPassword"
    placeholder="Confirm Password"
    required>
</div>

  <span id="confirm-password-error" style="color:red;font-size:12px;"></span>   
  <button type="submit" class="savebbtnn">Save Password</button>
  <div class="flex-center" style="margin-top: 20px;">
    <a href="/rythm/login/login.php" style="color: blue; text-decoration: underline; font-size: 14px;">Back to Login</a>
  </div>
</div>

<script>
function forgetpass(val) {
  alert("Forgot Password clicked");
    var password = document.getElementById('InputPassword').value;
    var confirmPassword = document.getElementById('ConfirmPassword').value;

    if (password.length < 8) {
        document.getElementById('password-error1').innerHTML =
            'Password must be at least 8 characters';
        return false;
    }

    document.getElementById('password-error1').innerHTML = '';

    if (password !== confirmPassword) {
        document.getElementById('confirm-password-error').innerHTML =
            'Passwords do not match';
        return false;
    }

    document.getElementById('confirm-password-error').innerHTML = '';
    return true;
}
</script>
</html>