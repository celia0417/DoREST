<?php

require_once 'tempResource.php';

class JSONRender_RestAbstract implements JSONRender_RestInterface, JSONRender_SerializeInterface {

    protected $idField;
    protected $table;
    protected $fields;
    protected $renameMap = array(// Change name of attribute
        'last_update' => 'lastUpdate'
    );
    protected $nestedMap = array();     // Increment the level  
    protected $linksMap = array();      //reference to other table

    public function delete($parseResult) {
        $parseResult['TYPE'] = 'DELETE';
        $parseResult['IDFIELD'] = $this->idField;

        $db = Database_DatabaseFactory::getInstance();
        $results = $db->passData($parseResult);
    }

    public function get($parseResult) {
        $parseResult['TYPE'] = 'GET';
        $parseResult['IDFIELD'] = $this->idField;

        if (empty($parseResult['FIELDS'])) {
            $parseResult['FIELDS'] = implode(',', $this->fields);
        }

        $db = Database_DatabaseFactory::getInstance();
        $results = $db->passData($parseResult);

        if ($parseResult['LIMIT'] !== '' && $parseResult['OFFSET'] !== '') {
            $this->addNavigationResponseHeaders($parseResult['LIMIT'], $parseResult['OFFSET'], $results['total']);
        }

        return $this->serialize($results['results']);
    }

    public function post($parseResult) {
        $parseResult['DATA'] = $this->deserialize($parseResult['DATA']);
        $parseResult['TYPE'] = 'POST';

        //pass the array to database and create a new record in database

        $db = Database_DatabaseFactory::getInstance();
        $results = $db->passData($parseResult);
    }

    public function put($parseResult) {
        $parseResult['TYPE'] = 'PUT';
        $parseResult['DATA'] = $this->deserialize($parseResult['DATA']);
        $parseResult['IDFIELD'] = $this->idField;

        $db = Database_DatabaseFactory::getInstance();
        $results = $db->passData($parseResult);
    }

    public function deserialize($json) {
        $invertedMap = array_flip($this->renameMap);
        $jsonArray = json_decode($json, true);

        $resultArray = array();

        foreach ($jsonArray as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $key2 => $val) {
                    $key3 = $key2;
                    if (isset($invertedMap[$key2])) {
                        $key3 = $invertedMap[$key2];
                    }
                    $resultArray[$key3] = $val;
                }
            } else {
                $key3 = $key;

                if (isset($invertedMap[$key])) {
                    $key3 = $invertedMap[$key];
                }

                $resultArray[$key3] = $value;
            }
        }

        return $resultArray;
    }

    public function serialize($results) {
        $resultsArray = array();

        foreach ($results as $result) {
            $resultsArray[] = $this->resultToArray($result);
        }

        return json_encode($resultsArray);
    }

    private function resultToArray($result) {
        $dataArray = array();
        $linksArray = array();


        foreach ($result as $key => $value) {
            if (array_key_exists($key, $this->linksMap)) {
                $linksArray[] = array(
                    'rel' => $this->linksMap[$key],
                    'url' => APP_URL . '/' . $this->linksMap[$key] . '/' . $value
                );
            } else if (array_key_exists($key, $this->nestedMap)) {
                $key2 = $this->nestedMap[$key];

                if (!isset($dataArray[$key2])) {
                    $dataArray[$key2] = array();
                }

                $key3 = $key;
                if (array_key_exists($key, $this->renameMap)) {
                    $key3 = $this->renameMap[$key];
                }

                $dataArray[$key2][$key3] = $value;
            } else {
                $key3 = $key;
                if (array_key_exists($key, $this->renameMap)) {
                    $key3 = $this->renameMap[$key];
                }

                $dataArray[$key3] = $value;
            }
        }

        $resultArray = array(
            'data' => $dataArray
        );

        if (count($linksArray) > 0) {
            $resultArray['links'] = $linksArray;
        }

        return $resultArray;
    }

    function __construct() {
        $this->idField = $this->table . '_id';
    }

    private function addNavigationResponseHeaders($limit, $offset, $total) {
        $headers = array();
        $next = $limit + $offset;
        $prev = $offset - $limit;
        $last = ceil($total / $limit) - 1;
        $selfUri = $_SERVER['REQUEST_URI'];


        $nextUri = preg_replace('/offset=[0-9]+/', 'offset=' . $next, $selfUri);
        $prevUri = preg_replace('/offset=[0-9]+/', 'offset=' . $prev, $selfUri);
        $firstUri = preg_replace('/offset=[0-9]+/', 'offset=0', $selfUri);
        $lastUri = preg_replace('/offset=[0-9]+/', 'offset=' . $last, $selfUri);

        //Mising last uri

        $headers[] = APP_URL . $selfUri . ';rel="self"';

        if ($next <= $last) {
            $headers[] = APP_URL . $nextUri . ';rel="next"';
        }

        if ($prev >= 0) {
            $headers[] = APP_URL . $prevUri . ';rel="previous"';
        }

        $headers[] = APP_URL . $firstUri . ';rel="first"';
        $headers[] = APP_URL . $lastUri . ';rel="last"';

        header('Links: ' . implode(',', $headers));
    }

}
