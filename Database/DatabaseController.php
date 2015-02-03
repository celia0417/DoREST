<?php

class Database_DatabaseController {

    protected $db;

    function __construct($host = '127.0.0.1', $dbName = 'sakila', $username = 'DoREST', $password = 'resting') {
        $this->db = new PDO('mysql:host=' . $host . ';dbname=' . $dbName . ';charset=utf8', $username, $password);
    }

    function passData($parseResult) {
        $resource = $parseResult["TABLES"];
        $q = $parseResult["Q"];
        $offset = $parseResult["OFFSET"];
        $limit = $parseResult["LIMIT"];
        $fields = $parseResult["FIELDS"];
        $type = $parseResult["TYPE"];

        // resource  
        if (!empty($resource)) {
            $table = $resource;
        } else {
            $errors[] = $resource . ' cannot be empty';
            $statusCode = 400;
        }

        //q
        $q_expression = null;

        if ($q) {
            $q_expression = $q;
        }


        //fields
        $displayFields = explode(',', $fields);
        $fields_display = implode(', ', $displayFields);


        $parseResult['fields_display'] = $fields_display;
        $parseResult['q_expression'] = $q_expression;

        if (!empty($errors)) {
            echo json_encode(array('errors' => $errors));
        }

        if ($type == 'DELETE') {
            return $this->deleteObject($parseResult);
        } elseif ($type == 'POST') {
            return $this->createObject($parseResult);
        } elseif ($type == 'PUT') {
            return $this->modifyObject($parseResult);
        } elseif ($type == 'GET') {
            return $this->getObject($parseResult);
        }
    }

    function deleteObject($dataArray) {
        $table = $dataArray["TABLES"];
        $q = $dataArray["Q"];
        $offset = $dataArray["OFFSET"];
        $limit = $dataArray["LIMIT"];
        $q_expression = $dataArray['q_expression'];
        $idField = $dataArray['IDFIELD'];

        $db = $this->db;

        $where = '';
        if ($dataArray['PRIMARYID']) {
            $where = ' WHERE ' . $idField . ' = ' . (int) $dataArray['PRIMARYID'];
        } else if ($q_expression) {
            $where = ' WHERE ' . $q_expression;
        }

        $limitString = '';
        if ($limit && $offset) {
            $limitString = " limit " . $offset . "," . $limit;
        }

        $sql = "DELETE FROM " . $table . $where . $limit;

        $query = $db->prepare($sql);
        
        if (!$query) {
            throw new Exception('Correct me');
        }

        return var_dump($query->fetchAll(PDO::FETCH_ASSOC));
    }

    function createObject($dataArray) {
        $table = $dataArray["TABLES"];
        $keys = array_keys($dataArray['DATA']);
        $columns = implode(', ', $keys);

        foreach ($keys as $key => $value) {
            $keys[$key] = ':' . $value;
        }

        $db = $this->db;

        $query = $db->prepare("INSERT INTO " . $table . "(" . $columns . ") VALUES(" . implode(', ', $keys) . ")");

        $query->execute($dataArray['DATA']);
        var_dump($query->errorInfo());
    }

    function modifyObject($dataArray) {
        $table = $dataArray["TABLES"];
        $keys = array_keys($dataArray['DATA']);
        $values = array_values($dataArray['DATA']);
        $idField = $dataArray['IDFIELD'];

        $values[] = $dataArray['PRIMARYID'];

        $set = implode(' = ?, ', $keys) . ' = ?';

        foreach ($keys as $key => $value) {
            $keys[$key] = ':' . $value;
        }

        $db = $this->db;

        $query = $db->prepare("UPDATE " . $table . " SET " . $set . " WHERE " . $idField . ' = ?');

        /*
         * Check for errors
         */
        $query->execute($values);
    }

    function getObject($dataArray) {
        $table = $dataArray["TABLES"];
        $q = $dataArray["Q"];
        $offset = $dataArray["OFFSET"];
        $limit = $dataArray["LIMIT"];
        $fields_display = $dataArray['fields_display'];
        $q_expression = $dataArray['q_expression'];
        $idField = $dataArray['IDFIELD'];

        $db = $this->db;

        $where = '';
        if ($dataArray['PRIMARYID']) {
            $where = ' WHERE ' . $idField . ' = ' . (int) $dataArray['PRIMARYID'];
        } else if ($q_expression) {
            $where = ' WHERE ' . $q_expression;
        }

        $limitString = '';
        if ($limit !== '' && $offset !== '') {
            $limitString = " limit " . $offset . "," . $limit;
        }

        $sql = "SELECT SQL_CALC_FOUND_ROWS " . $fields_display . " FROM " . $table . $where . $limitString;

        $query = $db->query($sql);
        
        if (!$query) {
            throw new Exception('Correct me');
        }

        $results = $query->fetchAll(PDO::FETCH_ASSOC);
        $count = $db->query('SELECT FOUND_ROWS();')->fetch(PDO::FETCH_COLUMN);
        
        
        return array(
            'total' => $count,
            'results' => $results
        );
    }

}
