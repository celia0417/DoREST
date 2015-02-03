<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$errors = array();
$statusCode = 200;


$availableResources = array(
    'stores' => 'store',
    'staff' => 'staff',
    'payments' => 'payment',
    'rentals' => 'rental',
    'temp' => 'temp'
);

$statusCodes = array();

$fields = array(
    'store' => array(
        'store_id',
        'manager_staff_id',
        'address_id',
        'last_update'
    ),
    'staff' => array(
        'staff_id',
        'email',
        'username'
    ),  
    'payment' => array(
        'payment_id'
    ),
    'rental' => array(
        'rental_id'
    ),
    'temp' => array(
        'id'
    )
);

?>