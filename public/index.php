<?php

defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/..'));

defined('APP_URL') || define('APP_URL', 'http://dev.do-rest.com');

function my_autoloader($class) {
    $parts = explode('_', $class);
    include APPLICATION_PATH . '/' . $parts[0] . '/' . $parts[1] . '.php';
}

spl_autoload_register('my_autoloader');

require_once APPLICATION_PATH . '/Config/config.php';

$urlParser = new Url_Parser($availableResources, $fields);

$parsed = $urlParser->parse();

$errors = $parsed['errors'];
$params = $parsed['params'];


if (count($errors)) {
    header('HTTP/1.1 400 Bad Request', true, 400);
    echo json_encode($errors);
} else {
    $class = 'JSONRender_' . ucfirst($params["resource"]);
    $jsonRender = new $class();

    $lowerMethod = strtolower($_SERVER['REQUEST_METHOD']);
    
    
    echo $jsonRender->$lowerMethod(array(
        'TABLES' => $params["resource"],
        'URL_RESOURCE' => $params["url_resource"],
        'PRIMARYID' => $params["primaryid"],
        'Q' => $params["q"],
        'OFFSET' => $params["offset"],
        'LIMIT' => $params["limit"],
        'FIELDS' => $params["fields"],
        'DATA' => $params['data']
    ));
}
