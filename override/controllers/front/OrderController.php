<?php

require_once(_PS_MODULE_DIR_ . 'itekcom_oussamasamia/classes/checkout/CheckoutHealthStep.php');

use \Itekcom_Oussamasamia\Checkout\CheckoutHealthStep;

class OrderController extends OrderControllerCore {

    protected function buildCheckoutProcess(CheckoutSession $session, $translator)
    {
        $checkoutProcess = new CheckoutProcess(
            $this->context,
            $session
        );
        $checkoutProcess
            ->addStep(new CheckoutPersonalInformationStep(
                $this->context,
                $translator,
                $this->makeLoginForm(),
                $this->makeCustomerForm()
            ))
            ->addStep(new CheckoutAddressesStep(
                $this->context,
                $translator,
                $this->makeAddressForm()
            ));

        /*Add health step*/
        $checkoutProcess->addStep(new CheckoutHealthStep(
            $this->context,
            $translator,
            null
        ));
        /*End Health Step*/


        if (!$this->context->cart->isVirtualCart()) {
            $checkoutDeliveryStep = new CheckoutDeliveryStep(
                $this->context,
                $translator
            );
            $checkoutDeliveryStep
                ->setRecyclablePackAllowed((bool) Configuration::get('PS_RECYCLABLE_PACK'))
                ->setGiftAllowed((bool) Configuration::get('PS_GIFT_WRAPPING'))
                ->setIncludeTaxes(
                    !Product::getTaxCalculationMethod((int) $this->context->cart->id_customer)
                    && (int) Configuration::get('PS_TAX')
                )
                ->setDisplayTaxesLabel((Configuration::get('PS_TAX') && !Configuration::get('AEUC_LABEL_TAX_INC_EXC')))
                ->setGiftCost(
                    $this->context->cart->getGiftWrappingPrice(
                        $checkoutDeliveryStep->getIncludeTaxes()
                    )
                );
            $checkoutProcess->addStep($checkoutDeliveryStep);
        }
        $checkoutProcess
            ->addStep(new CheckoutPaymentStep(
                $this->context,
                $translator,
                new PaymentOptionsFinder(),
                new ConditionsToApproveFinder(
                    $this->context,
                    $translator
                )
            ));
        return $checkoutProcess;
    }
}
