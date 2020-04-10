<?php

// Set include path to look for classes in the models directory, then in the controllers directory
set_include_path(get_include_path() . PATH_SEPARATOR . 'models' . PATH_SEPARATOR . 'controllers');

// Register the autoload function to automatically include classes
spl_autoload_register(array('Controller', 'autoload'));

/**
 * The Controller class is an abstract class used to handle user requests and make use of various
 * models and views
 *
 */
abstract class Controller
{
    const SESSION_NAME = 'SURVEYFORMAPP';
    const RUNTIME_EXCEPTION_VIEW = 'runtime_exception.php';

    protected $config;
    protected $dsn;
    protected $pdo;
    protected $viewVariables = array();

    /**
     * Get the filename of the view file containing HTML and PHP presentation logic
     * 
     * @return string returns the view filename
     */
    protected function getViewFilename()
    {
        return basename($_SERVER['SCRIPT_NAME']);
    }

    /**
     * Handle the page request
     *
     * @param array $request the page parameters from a form post or query string
     */
    abstract protected function handleRequest(&$request);

    /**
     * Handle an exception and display the error to the user
     *
     * @param Exception $e the Exception to be displayed
     */
    protected function handleError(Exception $e)
    {
        $this->assign('statusMessage', $e->getMessage());
    }

    /**
     * Assign a variable to be used in the view
     *
     * @param string $name the variable name
     * @param mixed $value the variable value
     */
    protected function assign($name, $value)
    {
        $this->viewVariables[$name] = $value;
    }

    /**
     * Display the view associated with the controller
     */
    protected function displayView($viewFilename)
    {
        chdir('views');

        if (! file_exists($viewFilename))
            throw new RuntimeException("Filename does not exist: $viewFilename");

        // Extract view variables into current scope
        extract($this->viewVariables);

        // Display the view
        require $viewFilename;
    }

    /**
     * Automatically load the necessary file for a given class
     *
     * @param string $class the class name to autoload
     */
    public static function autoload($class)
    {
        require $class . '.php';
        return true;
    }

    /**
     * Display the page - open the database, execute controller code, and display the view
     */
    public function display()
    {
        // Make sure requests and responses are utf-8
        header('Content-type: text/html; charset=utf-8');

        try
        {
            // Check to make sure required dependencies are installed
            $this->checkDependencies();

            // Open PDO database connection
            $this->openDatabase();

            // Handle the page request
            $this->handleRequest($_REQUEST);

            // Get view filename
            $viewFilename = $this->getViewFilename();

            // Display the view
            $this->displayView($viewFilename);
        }
        catch (RuntimeException $e)
        {
            $this->assign('statusMessage', $e->getMessage());
            $this->displayView(self::RUNTIME_EXCEPTION_VIEW);
        }
        catch (Exception $e)
        {
            // Handle exception
            $this->handleError($e);

            // Get view filename
            $viewFilename = $this->getViewFilename();

            // Display view
            if ($viewFilename)
                $this->displayView($viewFilename);
            else
                die($e->getMessage());
        }
    }

    /**
     * Start a new session with the session name defined in the 
     * SESSION_NAME class constant
     */
    protected function startSession()
    {
        session_name(self::SESSION_NAME);

        session_start();
    }

    /**
     * Get the current user sessions, or redirect to the login page
     */
    protected function getUserSession()
    {
        $this->startSession();

        if (!isset($_SESSION['login']))
            $this->redirect('login.php');

        return $_SESSION['login'];
    }

    /**
     * Redirect to another URL
     *
     * @param string $url the URL to redirect to
     */
    protected function redirect($url)
    {
        // Close session information
        if (session_id() != "")
            session_write_close();

        header("Location: $url");
        exit;
    }

    /**
     * Check for required dependencies and throw an exception if not all dependencies are found
     *
     * @throws RuntimeException if a required dependency is not found
     */
    protected function checkDependencies()
    {
        $missing = array();

        if (! extension_loaded('openssl'))
            $missing[] = 'openssl';

        if (! extension_loaded('pdo'))
            $missing[] = 'PDO';

        if (!empty($missing))
            throw new RuntimeException("The following PHP extensions are required:\n\n" . implode("\n", $missing));
    }

    /**
     * Open a PDO connection to the database and assign it to instance variable $pdo
     *
     * @throws RuntimeException if the database could not be opened
     */
    protected function openDatabase()
    {
        try
        {
            $host='localhost';
            $db = 'smartsurveys';
            $username = 'root';
            $password = '';

            $dsn = "mysql:host=$host;dbname=$db";
            $this->pdo = new PDO($dsn, $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->exec('SET FOREIGN_KEY_CHECKS = 1;');

            if (! $this->databaseTablesCreated())
                 throw new RuntimeException("Databases Tables are not found.");
        }
        catch (PDOException $e)
        {
            throw new RuntimeException('PDOException: ' . $e->getMessage());
        }
    }

    /**
     * Determine if database tables have already been created
     */
    protected function databaseTablesCreated()
    {
        $sql = "select count(*) FROM requester WHERE 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_NUM);
        if ($row = $stmt->fetch())
        {
            if ($row[0] > 0)
                return true;
        }
        return false;
    }
}

?>
