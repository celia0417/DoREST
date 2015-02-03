<?php
interface JSONRender_RestInterface{
    public function post($params);
    public function get($params);
    public function put($params);
    public function delete($params);
}