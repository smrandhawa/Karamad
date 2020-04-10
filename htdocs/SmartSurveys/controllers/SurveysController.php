<?php

/**
 * The SurveysController class is a Controller that shows a user a list of surveys 
 * in the database
 *
 */
class SurveysController extends Controller
{
    /**
     * Handle the page request
     *
     * @param array $request the page parameters from a form post or query string
     */
    protected function handleRequest(&$request)
    {
        $user = $this->getUserSession();
        $this->assign('user', $user);

        $surveys = Survey::queryRecords($this->pdo, array('sort' => 'survey_name'));
        if(!empty($surveys))
        {
            $surveysofusers = array();
            for($i=0; $i<sizeof($surveys);$i++)
            {

                if ($surveys[$i]->requester_id == $user->requester_id)
                {
                    $surveysofusers[]=$surveys[$i];
                }
            }
            $this->assign('surveys', $surveysofusers);     
        }
        else
        {
            $this->assign('surveys', $surveys);    
        }
        

        if (isset($request['status']) && $request['status'] == 'deleted')
            $this->assign('statusMessage', 'Survey deleted successfully');
    }
}

?>
