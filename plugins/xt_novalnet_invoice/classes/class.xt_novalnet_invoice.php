<?php
/*
 ###################################################
 #             Novalnet Payment file
 # ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 # @author    Novalnet AG
 # @copyright 2019 Novalnet 
 # @license   https://www.novalnet.de/payment-plugins/kostenlos/lizenz
 # @link      https://www.novalnet.de
 ###################################################
*/

defined('_VALID_CALL') or die('Direct Access is not allowed.');

include_once _SRV_WEBROOT . _SRV_WEB_PLUGINS . 'xt_novalnet_config/classes/class.novalnet.php';

/**
 * xt_novalnet_invoice Class
 */

class xt_novalnet_invoice
{
	/**
	 * Code for the gateway.
	 *
	 * @var string
	 */
	public $code         = 'xt_novalnet_invoice';
	
	/**
	 * Gateway shows Sub-Payments inside the Payment on the checkout.
	 *
	 * @var bool
	 */
	public $subpayments  = false;
	
	/**
	 * Gateway shows default iframe on the checkout.
	 *
	 * @var bool
	 */
	public $iframe       = false;
	
	/**
	 * Assign value for gateway post process
	 *
	 * @var bool
	 */
	public $external     = false;

	/**
	 * Settings of the gateway template.
	 *
	 * @var array
	 */
	public $data         = array();
     
	/**
	 * Constructor
	 *
	 */
    function xt_novalnet_invoice()
    {
		global $order;
        // Assign basic details to template
        $this->data = array_merge($this->data, Novalnet::get_basic_template_details($this->code));
		$this->data['js_date_picker_path']= _SRV_WEB_PLUGINS.'xt_novalnet_invoice/javascript/datepicker-'.$_SESSION['selected_language'].'.js';
        // Assign fraud module details to template
        Novalnet::get_fraud_module_details($this->data, $this->code);

        // Assign guarantee details to template
        Novalnet::get_guarantee_details($this->data, $this->code);
    }
    
    /**
     * Form additional parameters
     *
     * @param array $xt_novalnet_config
     * @param array $parameters
     */
    function additional_parameters($xt_novalnet_config, &$parameters) {
		global $order;

		// Assign the on-hold parameter
		$xt_novalnet_config->onhold_param( $parameters, $this->code );

		// Form fraud module parameters
		$xt_novalnet_config->form_fraud_module_params($parameters, $this->code);

		// Form guarantee payment parameters
		$xt_novalnet_config->form_guarantee_payment_params($parameters, $this->code);

		// Form payment parameters
		$due_date = intval(trim(XT_NOVALNET_INVOICE_DUE_DATE));
		if (!empty($due_date)) {
			$parameters['due_date'] = date('Y-m-d', strtotime('+' . $due_date . 'days'));
		}
		$parameters['invoice_type'] = 'INVOICE';
		$parameters['invoice_ref']  = 'BNR-' . trim(XT_NOVALNET_PRODUCT_ID) . '-' . $order->order_data['orders_id'];
		// Process the payment
		$xt_novalnet_config->proceed_payment($parameters, $this->code );
	}
}
