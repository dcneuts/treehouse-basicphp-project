<?php
// Execute only when form values have been submitted
// uses REQUEST METHOD to check POST status
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim(filter_input(INPUT_POST,"name",FILTER_SANITIZE_STRING));
    $email = trim(filter_input(INPUT_POST,"email",FILTER_SANITIZE_EMAIL));
    $details = trim(filter_input(INPUT_POST,"details",FILTER_SANITIZE_SPECIAL_CHARS));

// validation
    if ($name =="" || $email == "" || $details == "") {
        echo "Please fill in the required fields: Name, Email and Details";
        exit;
    }
// honeypot validation
    if ($_POST["address"] !="") {
        echo "Bad form input";
        exit;
    }

// PHPMailer integration for sending mail
    include_once("inc/phpmailer/PHPMailer.php");
    include_once("inc/phpmailer/Exception.php");
    $mail = new PHPMailer;
    if (!$mail->ValidateAddress($email)) {
        echo "Invalid Email Address";
        exit;
    } else {

        try {
            $email_body = "";
            $email_body .= "Name: " . $name . "\n";
            $email_body .= "Email: " . $email . "\n";
            $email_body .= "Details: " . $details . "\n";

            $mail->setFrom($email, $name);
            $mail->addAddress('dcneuts@gmail.com', 'Derek Neuts');     // Add a recipient

            //Content
            $mail->isHTML(false);                                  // Set email format to HTML
            $mail->Subject = 'Personal Media Library Suggestion from ' . $name;
            $mail->Body = $email_body;
        } catch (Exception $e) {
            echo 'Mailer Error: ' . $mail->ErrorInfo;
            exit;
        }

// Send and check
        if (!$mail->send()) {
            echo "Mailer Error: " . $mail->ErrorInfo;
        } else {
            echo "Message sent!";
        }
    }

    header("location:suggest.php?status=thanks");
}

$pageTitle = "Suggest a Media Item";
$section = "suggest";

include("inc/header.php"); ?>

<div class="section page">
    <div class="wrapper">
        <h1>Suggest a Media Item</h1>
        <?php if (isset($_GET["status"]) && $_GET["status"] == "thanks") {
            echo "<p>Thanks for the email! I&rsquo;ll check out your suggestion shortly!</p>";
} else { ?>

        <p>If you think there's something I&rsquo;m missing, let me know! Complete the form to send me an email.</p>
    </div>
    <form method="post" action="suggest.php">
        <table>
            <tr>
                <th><label for="name">Name</label></th>
                <td><input type="text" id="name" name="name"/></td>
            </tr>
            <tr>
                <th><label for="email">Email</label></th>
                <td><input type="text" id="email" name="email"/></td>
            </tr>
            <tr>
                <th><label for="category">Category</label></th>
                <td><select id="category" name="category">
                        <option value="">Select One</option>
                        <option value="Books">Book</option>
                        <option value="Movies">Movie</option>
                        <option value="Music">Music</option>
                    </select></td>
            </tr>
            <tr>
                <th><label for="title">Title</label></th>
                <td><input type="text" id="title" name="title"/></td>
            </tr>
            <tr>
                <th><label for="format">Format</label></th>
                <td><select id="format" name="format">
                        <option value="">Select One</option>
                        <option value="Books">Book</option>
                        <option value="Movies">Movie</option>
                        <option value="Music">Music</option>
                    </select></td>
            </tr>
            <tr>
                <th><label for="details">Suggest Item Details</label></th>
                <td><textarea name="details" id="details"></textarea></td>
            </tr>
            <tr style="display:none"><!--Honeypot for Bots-->
                <th><label for="address">Address</label></th>
                <td><input type="text" id="address" name="address" />
                <p>Please leave this field blank.</p></td>
            </tr>
        </table>
        <input type="submit" value="Send" />
    </form>
    <?php } ?>

</div>

<?php include("inc/footer.php"); ?>