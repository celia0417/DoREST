<?php

class JSONRender_Rental extends JSONRender_RestAbstract {

    function __construct() {
        $this->table = 'rental';
        $this->fields = array(
            'rental_id',
            'rental_date',
            'inventory_id',
            'customer_id',
            'return_date',
            'staff_id',
            'last_update'
        );
        
        
        $this->renameMap += array(
            'rental_id' => 'rentalId',
            'rental_date' => 'rentalDate',
            'inventory_id' => 'inventoryId',
            'customer_id' => 'customerID',
            'return_date' => 'returnDate',
            'staff_id' => 'staffId'
        );

        $this->linksMap = array(
            'rental_id' => 'rentals',
            'inventory_id' => 'inventories',
            'customer_id' => 'customers',
            'staff_id' => 'staff'
        );
    }
}

