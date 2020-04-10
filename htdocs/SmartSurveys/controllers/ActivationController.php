<?php

/**
 * The LoginController class is a Controller that allows a user to login
 *
 */
class ActivationController extends Controller
{



    /**
     * Handle the page request
     * 
     * @param array $request the page parameters from a form post or query string
     */
    protected function handleRequest(&$request)
    {

        if (isset($_GET['ac']) && empty($_GET['ac']) )
            throw new Exception('Activation Link does not work');


        if (isset($_GET['ac']))
        {
            $success = false;

            // Query the database by email
            $login = Requester::queryRecordByActivationCode($this->pdo, $_GET['ac']);

            if ($login)
            {
                $login->email_status =  1;
                $this->pdo->beginTransaction();
                $login->storeRecord($this->pdo);
                $this->pdo->commit();
            }
        }
    }

}

?>
