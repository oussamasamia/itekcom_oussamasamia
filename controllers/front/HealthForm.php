<?php

require_once(_PS_MODULE_DIR_ . 'itekcom_oussamasamia/classes/CustomerHealth.php');

use \Itekcom_Oussamasamia\CustomerHealth;

class Itekcom_OussamasamiaHealthFormModuleFrontController extends ModuleFrontController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function postProcess()
    {

        if (Tools::isSubmit('submitCustomerHealth')) {
            // Handle form submission logic
            $this->submitCustomerHealth();
        }
    }


    protected function submitCustomerHealth()
    {
        // Retrieve the submitted "doctor" value
        $doctor = Tools::getValue('doctor');


        // Update the current customer's "doctor" field
        $this->context->customer = new CustomerHealth($this->context->customer->id);
        $customer = $this->context->customer;
        $customer->doctor = $doctor;

        $ok = $customer->update();

        // Redirect back to the checkout step
        $checkoutStepUrl = $this->context->link->getPageLink(
            'order',
            true,
            null,
            array('submitCustomerHealth' => 1)
        );

        // Redirect back to the checkout step
        Tools::redirect($checkoutStepUrl);
    }
}