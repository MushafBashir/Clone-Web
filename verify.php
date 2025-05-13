<?php
include 'connect.php';

$verified = false;
$error = '';

if (isset($_POST['verify'])) {
    $email = $_POST['email'];
    $code = $_POST['code'];

    $query = "SELECT * FROM users WHERE email='$email' AND verify_code='$code'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $update = "UPDATE users SET is_verified=1, verify_code=NULL WHERE email='$email'";
        if ($conn->query($update)) {
            $verified = true;
            header("Location: index.php?verified=1");
            exit();
        } else {
            $error = "Database update failed. Please try again.";
        }
    } else {
        $error = "Invalid verification code.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Email Verification</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
  <style>
    .verify-container {
        max-width: 400px;
        margin: 8% auto;
        padding: 40px;
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
        border-radius: 12px;
        background: #fff;
        text-align: center;
    }
    .verify-container h2 {
        margin-bottom: 20px;
    }
    .verify-container input {
        width: 100%;
        padding: 12px;
        margin: 10px 0;
        border-radius: 8px;
        border: 1px solid #ccc;
        font-size: 16px;
    }
    .verify-container button {
        background-color: #007bff;
        color: white;
        padding: 12px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        cursor: pointer;
    }
    .verify-container .error {
        color: red;
        margin-top: 10px;
    }
  </style>
</head>
<body>

  <div class="verify-container">
    <h2>Email Verification</h2>
    <p>Please enter the 6-digit code sent to your email.</p>

    <form method="post">
      <input type="hidden" name="email" value="<?php echo htmlspecialchars($_GET['email'] ?? '', ENT_QUOTES); ?>">
      <input type="text" name="code" maxlength="6" placeholder="Verification Code" required>
      <button type="submit" name="verify">Verify</button>
    </form>

    <?php if ($error): ?>
      <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>
  </div>

</body>
</html>
