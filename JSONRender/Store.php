<?php

class JSONRender_Store extends JSONRender_RestAbstract {

    function __construct() {
        $this->table = 'store';
        $this->fields = array(
            'store_id',
            'manager_staff_id',
            'address_id',
            'last_update'
        );
        
        $this->linksMap = array(
            'store_id' => 'stores',
            'manager_staff_id' => 'staff',
            'address_id' => 'addresses'
        );
        
    }
}
