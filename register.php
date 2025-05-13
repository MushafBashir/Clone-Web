<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Manual includes (adjust path if needed)
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

include 'connect.php';

// ========== SIGN UP ==========
if (isset($_POST['signUp'])) {
    $firstName = $_POST['fName'];
    $lastName = $_POST['lName'];
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    $code = rand(100000, 999999); // 6-digit code

    $checkEmail = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($checkEmail);

    if ($result->num_rows > 0) {
        echo "Email Address Already Exists!";
    } else {
        $insertQuery = "INSERT INTO users (firstName, lastName, email, password, verify_code) 
                        VALUES ('$firstName', '$lastName', '$email', '$password', '$code')";

        if ($conn->query($insertQuery) === TRUE) {
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'asadbashir200003@gmail.com'; // replace with your Gmail
                $mail->Password = 'isrz ejdb kzma xlar';    // replace with your Gmail app password
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('your_email@gmail.com', 'Software Engineering Project');
                $mail->addAddress($email, "$firstName $lastName");

                $mail->isHTML(true);
                $mail->Subject = 'Your Email Verification Code';
                $mail->Body    = "Hi $firstName,<br><br>Your verification code is: <b>$code</b><br>Please enter it on the verification page to complete your registration.";

                $mail->send();
                header("Location: verify.php?email=" . urlencode($email));
                exit();
            } catch (Exception $e) {
                echo "Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            echo "Error: " . $conn->error;
        }
    }
}

// ========== SIGN IN ==========
if (isset($_POST['signIn'])) {
    $email = $_POST['email'];
    $password = md5($_POST['password']);

    $sql = "SELECT * FROM users WHERE email='$email' AND password='$password' AND is_verified=1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        session_start();
        $row = $result->fetch_assoc();
        $_SESSION['email'] = $row['email'];
        header("Location: homepage.php");
        exit();
    } else {
        echo "Login failed. Either email is not verified or credentials are incorrect.";
    }
}
?>


