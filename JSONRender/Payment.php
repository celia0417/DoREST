<?php

class JSONRender_Payment extends JSONRender_RestAbstract {

    function __construct() {
        $this->table = 'payment';
        $this->fields = array(
            'payment_id', 
            'customer_id',
            'staff_id',
            'rental_id',
            'amount',
            'payment_date',
            'last_update'
        );
        
        $this->renameMap += array(
            'payment_id' => 'paymentId',
            'payment_date' => 'paymentDate'
        );
        
        $this->linksMap = array(
            'customer_id' => 'customers',
            'staff_id' => 'staff',
            'rental_id' => 'rentals',
        );
                
    }

}
