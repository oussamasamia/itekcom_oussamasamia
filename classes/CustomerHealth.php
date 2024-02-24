<?php


/**
 * Class Health.
 */
class CustomerHealth extends Customer
{
    /** @var string Doctor name */
    public $doctor;


    public function __construct($id = null)
    {
        parent::__construct($id);
    }

}