{extends file='checkout/_partials/steps/checkout-step.tpl'}
{block name='step_content'}
    <form action="{$link->getModuleLink('itekcom_oussamasamia', 'healthform')}" id="customerhealth-form"
          class="js-customer-form"
          method="post">

        <div class="form-group row ">
            <label class="col-md-3 form-control-label required" for="field-doctor">
                Doctor
            </label>
            <div class="col-md-6 js-input-column">
                <input id="field-doctor" class="form-control" name="doctor" type="text" required="required">
            </div>
        </div>

        {block name='customerhealth_form_footer'}
            <footer class="form-footer clearfix">
                <input type="hidden" name="submitCustomerHealth" value="1">
                {block "form_buttons"}
                    <button class="btn btn-primary form-control-submit float-xs-right"
                            data-link-action="save-customerhealth"
                            type="submit">
                        {l s='Save' d='Shop.Theme.Actions'}
                    </button>
                {/block}
            </footer>
        {/block}
    </form>
{/block}
