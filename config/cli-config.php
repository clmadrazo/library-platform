<?php
use Doctrine\ORM\Tools\Console\ConsoleRunner;

// replace with file to your own project bootstrap
require_once 'init_autoloader.php';

// replace with mechanism to retrieve EntityManager in your app
//$entityManager = GetEntityManager();

return ConsoleRunner::createHelperSet($entityManager);
