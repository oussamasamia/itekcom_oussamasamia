<?php


namespace Itekcom_Oussamasamia\Checkout;

use AbstractCheckoutStep;
use Context;
use CustomerHealthFormCore;
use Symfony\Contracts\Translation\TranslatorInterface;

class  CheckoutHealthStep extends AbstractCheckoutStep
{
    protected $template = 'module:itekcom_oussamasamia/views/templates/front/health-form.tpl';

    private $healthForm;

    /**
     * @param Context $context
     * @param TranslatorInterface $translator
     * @param CustomerHealthFormCore $healthForm
     */
    public function __construct(
        Context $context,
        TranslatorInterface $translator,
        CustomerHealthFormCore $healthForm = null
    ) {

        parent::__construct($context, $translator);
        $this->healthForm = $healthForm;

    }

    public function handleRequest(array $requestParameters = [])
    {
        // TODO: Implement handleRequest() method.
    }

    public function render(array $extraParams = [])
    {

        return $this->renderTemplate(
            $this->getTemplate(),
            $extraParams
        );

    }
}
