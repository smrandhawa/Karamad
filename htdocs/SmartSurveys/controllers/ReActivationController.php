<?php

/**
 * The LoginController class is a Controller that allows a user to login
 *
 */
class ReActivationController extends Controller
{


    public $ac = '';
    /**
     * Handle the page request
     * 
     * @param array $request the page parameters from a form post or query string
     */
    protected function handleRequest(&$request)
    {

        if (isset($_GET['ac']) && empty($_GET['ac']) )
            throw new Exception('Activation Link does not work. Check your email again');

        if (isset($_POST['ac']) && empty($_POST['ac']) )
            throw new Exception('Activation Link does not work. Check your email again');

        $ac = '';
        
        if (isset($_GET['ac']))
        {
            $ac = $_GET['ac'];
            $this->assign('ac', $ac);

        }
        
        if (isset($_POST['ac']))
        {
            $ac = $_POST['ac'];
            $this->assign('ac', $ac);
        }

        if (isset($_POST['npassword']) && isset($_POST['cpassword']) && empty($_POST['npassword']) && empty($_POST['cpassword']))
            throw new Exception('Please enter your new password and confirm it also.');

        if (isset($_POST['npassword']) && isset($_POST['cpassword']))
        {
            
            $login = Requester::queryRecordByActivationCodeEmailActivated($this->pdo, $ac);

            if ($login)
            {
                if ($_POST['npassword'] == $_POST['cpassword'] )
                {
                    $login->password = Requester::cryptPassword($_POST['npassword']);
                    $this->pdo->beginTransaction();

                    $login->storeRecord($this->pdo);

                    $this->pdo->commit();

                    $this->redirect('login.php?statusMessage=PS');
                }
                else
                {
                    throw new Exception("Passwords don't match. Please enter the same password in both the fields.");
                }
            }
            else
            {
                throw new Exception($login."Activation Link is not valid. Kindly, reset your password again");
            }
            
        }

    }

}

?>
