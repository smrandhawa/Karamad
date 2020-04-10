<?php

$_FILES = [];

require 'controllers/Controller.php';

$page = new SurveysController;
$page->display();

?>
