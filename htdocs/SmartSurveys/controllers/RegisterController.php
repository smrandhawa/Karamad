<?php

/**
 * The LoginController class is a Controller that allows a user to login
 *
 */
class RegisterController extends Controller
{
    /**
     * Handle the page request
     * 
     * @param array $request the page parameters from a form post or query string
     */
    protected function handleRequest(&$request)
    {

        if (isset($_POST['email']) && isset($_POST['password']) && empty($_POST['email']) && empty($_POST['password']))
            throw new Exception('Please enter your e-mail and password.');

        if (!empty($_POST['email']) && empty($_POST['password']))
            throw new Exception('Please enter your password.');

        if (isset($_POST['first_name']) && empty($_POST['first_name']))
            throw new Exception('Please enter your first name.');

        if (!empty($_POST['first_name']) && empty($_POST['last_name']))
            throw new Exception('Please enter your last name.');

        if (isset($_POST['email']) && isset($_POST['password']))
        {
            $success = false;

            // Query the database by email
            $login = Requester::queryRecordByEmail($this->pdo, $_POST['email']);

            if ($login)
            {
                throw new Exception("E-mail already exists.");
            }
            else
            {
                $login = new Requester;
                $login->updateValues($request);

                if (!empty($request['password']))
                    $login->password = Requester::cryptPassword($request['password']);

                //if (!empty($request['first_name']))
                    //$login->first_name = $request['first_name'];last_name

                $login->activation_code =  md5(rand());
                $login->email_status =  0;

                $this->pdo->beginTransaction();

                $login->storeRecord($this->pdo);

                $this->pdo->commit();

                $login = Requester::queryRecordByEmail($this->pdo, $_POST['email']);

                if ($login)
                {
                    require_once "phpmailer/class.phpmailer.php";

                    $message = '<html><head>
                               <title>Email Verification</title>
                               </head>
                               <body>';
                    $message .= '<h3>Hello ' . $login->first_name . ',</h3>';
                    $message .= '<p>Thank you for registering with Smart Surveys. <a href="'.'http://127.0.0.1/SmartSurveys/'.'activate.php?ac=' . $login->activation_code . '"> Kindly, Click to Activate your Account.</a>';
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

                    $this->redirect('thank_you_register.php');

                }

            }

        }
    }

}

?>
