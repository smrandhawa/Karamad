<?php

/**
 * The LoginController class is a Controller that allows a user to login
 *
 */
class ForgotController extends Controller
{
    /**
     * Handle the page request
     * 
     * @param array $request the page parameters from a form post or query string
     */
    protected function handleRequest(&$request)
    {

        if (isset($_POST['email']) && empty($_POST['email']))
            throw new Exception('Please enter your e-mail');

        if (isset($_POST['email']))
        {
            $success = false;

            // Query the database by email
            $login = Requester::queryRecordByEmail($this->pdo, $_POST['email']);

            if ($login)
            {

                if ($login->email_status > 0)
                {
                    require_once "phpmailer/class.phpmailer.php";

                    $message = '<html><head>
                               <title>Email Verification</title>
                               </head>
                               <body>';
                    $message .= '<h3>Hello ' . $login->first_name . ',</h3>';
                    $message .= '<p>It seems you Forgot your Password. <a href="'.'http://127.0.0.1/SmartSurveys/'.'reactivate.php?ac=' . $login->activation_code . '"> Kindly, Click to reset your Password.</a>';
                    $message .= "</body></html>";

                    // php mailer code starts
                    $mail = new PHPMailer(true);
                    // telling the class to use SMTP
                    $mail->IsSMTP();
                    // enable SMTP authentication
                    $mail->SMTPAuth = true;   
                    // sets the prefix to the server
                    $mail->SMTPSecure = "ssl"; 
                    // sets GMAIL as the SMTP server
                    $mail->Host = "smtp.gmail.com"; 
                    // set the SMTP port for the GMAIL server
                    $mail->Port = 465; 

                    // set your username here
                    $mail->Username = 'randhawaay@gmail.com';
                    $mail->Password = 'jqplhbfegsalydlg';

                    // set your subject
                    $mail->Subject = trim("Email Verifcation from Smart Surveys");

                    // sending mail from
                    $mail->SetFrom('randhawaay@gmail.com', 'Shan');
                    // sending to
                    $mail->AddAddress($login->email);
                    // set the message
                    $mail->MsgHTML($message);

                    try {
                      $mail->send();
                    } catch (Exception $ex) {
                      echo $msg = $ex->getMessage();
                    }
                    $this->redirect('thank_you_forgot.php');
                }
                else
                {
                    throw new Exception("E-mail is not activated. Check your email for the activation link or Use the register link below to register yourself.");
                }
            }
            else
            {
                
                throw new Exception("E-mail doesn't exists. Use the register link below to register yourself.");


            }

        }
    }

}

?>
