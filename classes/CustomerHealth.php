<?php

namespace Itekcom_Oussamasamia;


use Customer;
use Db;

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

        self::$definition['fields']['doctor'] = ['type' => self::TYPE_STRING, 'validate' => 'isGenericName'];

    }


    public function update($nullValues = false)
    {
        // Set the 'doctor' field value
        if (isset($this->doctor)) {
            $this->doctor = pSQL($this->doctor);
        }

        // Call the parent update method to update other fields
        $success = parent::update($nullValues);

        // Update the 'doctor' field separately if it's set
        if ($success && isset($this->doctor)) {
            $sql = 'UPDATE `' . _DB_PREFIX_ . 'customer`
                    SET `doctor` = \'' . $this->doctor . '\'
                    WHERE `id_customer` = ' . (int)$this->id;
            $success = Db::getInstance()->execute($sql);
        }

        return $success;
    }

}