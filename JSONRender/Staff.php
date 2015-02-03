<?php

class JSONRender_Staff extends JSONRender_RestAbstract {

    function __construct() {
        $this->table = 'staff';        
        
        $this->fields = array(
            'staff_id',
            'first_name',
            'last_name',
            'address_id',
            'email',
            'store_id',
            'active',
            'username',
            'last_update'
        );
        
        
        $this->renameMap += array(
            'first_name' => 'firstName',
            'last_name' => 'lastName',
            'staff_id' => 'id'
        );

        $this->nestedMap = array(
            'first_name' => 'name',
            'last_name' => 'name',
        );

        $this->linksMap = array(
            'address_id' => 'addresses',
            'store_id' => 'stores'
        );
        
        parent::__construct();
    }

}
