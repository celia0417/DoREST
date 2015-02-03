<?php

class Url_Parser {

    private $availableResources;
    private $fields;

    function __construct($availableResources, $fields) {
        $this->availableResources = $availableResources;
        $this->fields = $fields;
    }

    public function parse() {
        $availableResources = $this->availableResources;
        $fields = $this->fields;
        $errors = array();


        $params = array(
            'resource' => '',
            'q' => "",
            'primaryid' => "",
            'offset' => '',
            'limit' => '',
            'fields' => '',
            'fields_display' => array(),
            'data' => ''
        );

        switch (strtoupper($_SERVER['REQUEST_METHOD'])) {
            case 'GET':
            case 'DELETE':
                $matches = array();
                $resource = null;
                $table = null;

                if (!isset($_SERVER['REDIRECT_URL']))
                    break;
                if (preg_match('/[a-zA-Z]+\/[0-9]+|[a-zA-Z]+/', $_SERVER['REDIRECT_URL'], $matches)) {
                    $matches = explode('/', $matches[0]);
                    $resource = $matches[0];
                    if (isset($matches[1]))
                        $params['primaryid'] = $matches[1];

                    if (isset($availableResources[$resource])) {
                        $table = $availableResources[$resource];
                    } else {
                        $errors[] = 'Resource ' . $resource . ' does not exist';
                        $statusCode = 400;
                    }
                }

                if ($table) {
                    $availableFields = $fields[$table];
                    $params['resource'] = $table;
                    foreach ($_GET as $field => $value) {
                        switch ($field) {
                            case 'fields':
                                $params['fields'] = $value = str_replace("\"", "", $value);
                                $displayFields = explode(',', $value);
                                foreach ($displayFields as $displayField) {
                                    if (in_array($displayField, $availableFields)) {
                                        $params['fields_display'][] = $displayField;
                                    } else {
                                        $errors[] = 'The field ' . $displayField . ' does not exist';
                                        $statusCode = 400;
                                    }
                                }
                                break;
                            case 'q':
                                $params['q'] = $value = str_replace(array("\"", "AND", "OR"), array("", " AND ", " OR "), $value);
                                foreach (preg_split("/AND|OR|=| |[0-9]+/", $value) as $s) {
                                    if ($s != "" && $s[0] != '\'' && $s[0] != '.') {
                                        $params['fields_display'][] = $s;
                                    }
                                }
                                break;
                            case 'limit':
                                $params['limit'] = (int) $value;
                                break;
                            case 'offset':
                                $params['offset'] = (int) $value;
                                break;
                            default:
                                $errors[] = 'The field ' . $field . ' is not valid';
                                $statusCode = 400;
                                break;
                        }
                    }
                }
                break;
            case 'POST':
            case 'PUT':
                $matches = array();
                $resource = null;
                $table = null;
                if (!isset($_SERVER['REDIRECT_URL']))
                    break;
                if (preg_match('/[a-zA-Z]+\/[0-9]+|[a-zA-Z]+/', $_SERVER['REDIRECT_URL'], $matches)) {
                    $matches = explode('/', $matches[0]);
                    $resource = $matches[0];
                    if (isset($matches[1]))
                        $params['primaryid'] = $matches[1];
                    if (isset($availableResources[$resource])) {
                        $table = $availableResources[$resource];
                    } else {
                        $errors[] = 'Resource ' . $resource . ' does not exist';
                        $statusCode = 400;
                    }
                }

                if ($table) {
                    $availableFields = $fields[$table];
                    $params['resource'] = $table;
                    foreach ($_GET as $field => $value) {
                        switch ($field) {
                            case 'fields':
                                $params['fields'] = $value = str_replace("\"", "", $value);
                                $displayFields = explode(',', $value);
                                foreach ($displayFields as $displayField) {
                                    if (in_array($displayField, $availableFields)) {
                                        $params['fields_display'][] = $displayField;
                                    } else {
                                        $errors[] = 'The field ' . $displayField . ' does not exist';
                                        $statusCode = 400;
                                    }
                                }
                                break;
                            case 'q':
                                $params['q'] = $value = str_replace(array("\"", "AND", "OR"), array("", " AND ", " OR "), $value);
                                foreach (preg_split("/AND|OR|=| |[0-9]+/", $value) as $s) {
                                    if ($s != "" && $s[0] != '\'' && $s[0] != '.') {
                                        $params['fields_display'][] = $s;
                                    }
                                }
                                break;
                            default:
                                $errors[] = 'The field ' . $field . ' is not valid';
                                $statusCode = 400;
                                break;
                        }
                    }
                }
                $params['data'] = file_get_contents('php://input');
                
                if(!$params['data']){
                    $errors[] = 'No data received';
                    $statusCode = 400;
                }
                
                break;
        }

        $params['url_resource'] = $resource;
        
        return array(
            'errors' => $errors,
            'params' => $params
        );
    }

}
