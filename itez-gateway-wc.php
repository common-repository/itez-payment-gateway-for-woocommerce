<?php
/*
 * Plugin Name: Itez Payment Gateway for WooCommerce
 * Plugin URI: https://itez.com/
 * Description: Take bitcoin payments on your store.
 * Author: Itez.com
 * Author URI: https://itez.com
 * Version: 1.0.0
 *

 /*
 * This action hook registers our PHP class as a WooCommerce payment gateway
 */
add_filter( 'woocommerce_payment_gateways', 'itez_add_gateway_class' );
function itez_add_gateway_class( $gateways ) {
	$gateways[] = 'WC_itez_Gateway'; // your class name is here
	return $gateways;
}

			add_filter( 'page_template', 'fw_reserve_page_template' );
			function fw_reserve_page_template( $page_template ){
			    if ( is_wc_endpoint_url( 'order-pay' ) ) {

			        $page_template = dirname( __FILE__ ) . '/views/pay-template.php';
			    }
			    return $page_template;
			}

			add_action( 'woocommerce_init', 'wc_init' );
			function wc_init(){
			    // get_woocommerce_currency() will not throw error here.

				//Disable Itez if total less 30 USD
				global $currency_code;
				$currency_code = get_woocommerce_currency();
				
			}

/*
 * The class itself, please note that it is inside plugins_loaded action hook
 */
add_action( 'plugins_loaded', 'itez_init_gateway_class' );
function itez_init_gateway_class() {
 
	class WC_itez_Gateway extends WC_Payment_Gateway {
 
 		/**
 		 * Class constructor, more about it in Step 3
 		 */
 		public function __construct() {
 
			$this->id = 'itez'; // payment gateway plugin ID
			$this->icon = ''; // URL of the icon that will be displayed on checkout page near your gateway name
			$this->has_fields = true; // in case you need a custom credit card form
			$this->method_title = 'Itez Pay';
			$this->method_description = 'Itez payment gateway'; // will be displayed on the options page
		 
			// gateways can support subscriptions, refunds, saved payment methods,
			// but in this tutorial we begin with simple payments
			$this->supports = array(
				'products'
			);
		 
			// Method with all the options fields
			$this->init_form_fields();
		 
			// Load the settings.
			$this->init_settings();
			$this->title = $this->get_option( 'title' );
			$this->description = $this->get_option( 'description' );
			$this->currency = $this->get_option( 'currency' );
			$this->wallet = $this->get_option( 'wallet' );
			$this->enabled = $this->get_option( 'enabled' );
			$this->testmode = 'yes' === $this->get_option( 'testmode' );
			$this->private_key = $this->testmode ? $this->get_option( 'test_private_key' ) : $this->get_option( 'private_key' );
			$this->publishable_key = $this->testmode ? $this->get_option( 'test_publishable_key' ) : $this->get_option( 'publishable_key' );
		 
		    add_action( 'woocommerce_receipt_itezpay', 	array( $this, 'payment_page' ) );
			// This action hook saves the settings
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		 
			// We need custom JavaScript to obtain a token
			add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );
		 
			// You can also register a webhook here
			add_action( 'woocommerce_api_itezpay', array( $this, 'webhook' ) );
 
 			// Init global variables
			global $key1;
			global $key2;
			global $currency_pay;
			global $wallet_pay;

 			// Set global variables
			$key1 = $this->private_key; 
			$key2 = $this->publishable_key; 
			$currency_pay = $this->currency; 
			$wallet_pay = $this->wallet; 
		 }
 
		/**
 		 * Plugin options, we deal with it in Step 3 too
 		 */
 		public function init_form_fields(){
 
			$this->form_fields = array(
				'enabled' => array(
					'title'       => 'Enable/Disable',
					'label'       => 'Enable itez Gateway',
					'type'        => 'checkbox',
					'description' => '',
					'default'     => 'no'
				),
				'title' => array(
					'title'       => 'Title',
					'type'        => 'text',
					'description' => 'This controls the title which the user sees during checkout.',
					'default'     => 'Bank Card by Itez',
					'desc_tip'    => true,
				),
				'description' => array(
					'title'       => 'Description',
					'type'        => 'textarea',
					'description' => 'This controls the description which the user sees during checkout.',
					'default'     => 'Pay with your credit card via Itez payment gateway.',
				),
				'wallet' => array(
					'title'       => 'BTC wallet address',
					'type'        => 'text'
				),
				'currency' => array(
					'title'       => __( 'Store currency', 'woocommerce' ),
					'type'        => 'select',
					'class'       => 'wc-enhanced-select',
					'default'     => 'USD',
					'options'     => array(
						'RUB' => __( 'Russian Ruble', 'woocommerce' ),
						'EUR' => __( 'EURO', 'woocommerce' ),
						'USD' => __( 'US Dollar', 'woocommerce' ),
						'GBP' => __( 'Pound Sterling', 'woocommerce' ),
						'UAH' => __( 'Ukrainian Hryvnia', 'woocommerce' ),
						'INR' => __( 'Indian Rupee', 'woocommerce' ),
						'KRW' => __( 'Korean Won', 'woocommerce' ),
						'TRY' => __( 'Turkish Lira', 'woocommerce' ),
						'BRL' => __( 'Brazilian Real', 'woocommerce' ),
						'AUD' => __( 'Australian Dollar', 'woocommerce' ),
						'HKD' => __( 'Hong Kong Dollar', 'woocommerce' ),
						'TWD' => __( 'Taiwan New Dollars', 'woocommerce' ),
					),
                ),
                /*
				'testmode' => array(
					'title'       => 'Test mode',
					'label'       => 'Enable Test Mode',
					'type'        => 'checkbox',
					'description' => 'Place the payment gateway in test mode using test API keys.',
					'default'     => 'yes',
					'desc_tip'    => true,
				),
				'test_publishable_key' => array(
					'title'       => 'Test Itez Secret (Key)',
					'type'        => 'text'
				),
				'test_private_key' => array(
					'title'       => 'Test Partner Token',
					'type'        => 'text',
				),
				*/
				'publishable_key' => array(
					'title'       => 'Itez Secret (Key)',
					'type'        => 'text'
				),
				'private_key' => array(
					'title'       => 'Partner Token',
					'type'        => 'text'
				)
			);
		}
 
		/**
		 * You will need it if you want your custom credit card form, Step 4 is about it
		 */
		public function payment_fields() {
 
			// ok, let's display some description before the payment form
			if ( $this->description ) {
				// you can instructions for test mode, I mean test card numbers etc.
				if ( $this->testmode ) {
					$this->description .= ' TEST MODE ENABLED. In test mode, you can use the card numbers listed in <a href="#" target="_blank" rel="noopener noreferrer">documentation</a>.';
					$this->description  = trim( $this->description );
				}
				// display the description with <p> tags etc.
				echo wpautop( wp_kses_post( $this->description ) );
			}
		 
			// I will echo() the form, but you can close PHP tags and print it directly in HTML
			echo '<fieldset id="wc-' . esc_attr( $this->id ) . '-cc-form" class="wc-credit-card-form wc-payment-form" style="background:transparent;">';
		 
			// Add this action hook if you want your custom payment gateway to support it
			do_action( 'woocommerce_credit_card_form_start', $this->id );
		 
			// I recommend to use inique IDs, because other gateways could already use #ccNo, #expdate, #cvc
			echo '<div class="clear"></div><input name="customer_email" type="hidden" value="'.$this->id.'"><input name="order_amount" type="hidden" value="">';
		 
			do_action( 'woocommerce_credit_card_form_end', $this->id );
		 
			echo '<div class="clear"></div></fieldset>';
		 
		}
 
		/*
		 * Custom CSS and JS, in most cases required only when you decided to go with a custom credit card form
		 */
	 	public function payment_scripts() {
 
			// we need JavaScript to process a token only on cart/checkout pages, right?
			if ( ! is_cart() && ! is_checkout() && ! isset( $_GET['pay_for_order'] ) ) {
				return;
			}
		 
			// if our payment gateway is disabled, we do not have to enqueue JS too
			if ( 'no' === $this->enabled ) {
				return;
			}
		 
			// no reason to enqueue JavaScript if API keys are not set
			if ( empty( $this->private_key ) || empty( $this->publishable_key ) ) {
				return;
			}
		 
			// do not work with card detailes without SSL unless your website is in a test mode
			if ( ! $this->testmode && ! is_ssl() ) {
				return;
			}

			// in most payment processors you have to use PUBLIC KEY to obtain a token
			wp_localize_script( 'woocommerce_itez', 'itez_params', array(
				'publishableKey' => $this->publishable_key
			) );
		 
			wp_enqueue_script( 'woocommerce_itez' );
		 
		}
 
		/*
 		 * Fields validation, more in Step 5
		 */
		public function validate_fields(){
		 
		}
 
		/*
		 * We're processing the payments here, everything about it is in Step 5
		 */
		public function process_payment( $order_id ) {
			
			global $woocommerce;
			
			$order = new WC_Order( $order_id );

			$order->update_status( 'pending', '', true );
				
			return array(
				'result'    => 'success',
				'redirect'  => add_query_arg( 'key', $order->order_key, add_query_arg( 'order-pay', $order_id, $order->get_checkout_payment_url( true ) ) )
			);
		 
		}


		
		// Output iframe
		public function payment_page( $order_id ) { 
            //$this->cpgwwc_addError("Проверка заказа");
			global $woocommerce;			
			$order = new WC_Order( $order_id );

		}
 
		/*
		 * In case you need a webhook, like PayPal IPN etc
		 */
		public function webhook() {

		    $json = file_get_contents('php://input');
		    $jsonArray = json_decode($json, true);

		    $root_key = "operation_data";
		    $key = "status";
		    $key2 = "customer_info";
		    $key21 = "account";
		    $key3 = "operation_info";
		    $key31 = "from";
		    $key32 = "amount";
		    $key4 = "partner_info";
		    $key41 = "operation_id";

		    $note1 = $jsonArray[$root_key][$key];
		    $note2 = $jsonArray[$root_key][$key2][$key21];
		    $note3 = $jsonArray[$root_key][$key3][$key31][$key32];
		    $note4 = $jsonArray[$root_key][$key4][$key41];

		    $customer_user_email = $note2;

			$order = wc_get_order($note4);

			if (!empty($note4)) {
				$order->payment_complete();
				$order->reduce_order_stock();
				$order->update_status( 'processing', '', true );
			}
		}
 	}
}

add_filter( 'woocommerce_available_payment_gateways', 'conditional_available_payment_gateways', 20, 1 );

function conditional_available_payment_gateways( $available_gateways ) {
	
	if( is_admin() ) return $available_gateways; // Only for frontend
	
	if ( WC()->cart->total < 30 && get_woocommerce_currency() == 'EUR' ) {
		unset( $available_gateways['itez'] );
	} else if ( WC()->cart->total < 2000 && get_woocommerce_currency() == 'RUB' ) {
		unset( $available_gateways['itez'] );
	} else if ( WC()->cart->total < 30 && get_woocommerce_currency() == 'USD' ) {
		unset( $available_gateways['itez'] );
	} else if ( WC()->cart->total < 1000 && get_woocommerce_currency() == 'UAH' ) {
		unset( $available_gateways['itez'] );
	} else if ( WC()->cart->total < 3000 && get_woocommerce_currency() == 'INR' ) {
		unset( $available_gateways['itez'] );
	} else if ( WC()->cart->total < 40000000 && get_woocommerce_currency() == 'KRW' ) {
		unset( $available_gateways['itez'] );
	} else if ( WC()->cart->total < 300 && get_woocommerce_currency() == 'TRY' ) {
		unset( $available_gateways['itez'] );
	} else if ( WC()->cart->total < 30 && get_woocommerce_currency() == 'GBP' ) {
		unset( $available_gateways['itez'] );
	} else if ( WC()->cart->total < 200 && get_woocommerce_currency() == 'BRL' ) {
		unset( $available_gateways['itez'] );
	} else if ( WC()->cart->total < 50 && get_woocommerce_currency() == 'AUD' ) {
		unset( $available_gateways['itez'] );
	} else if ( WC()->cart->total < 1000 && get_woocommerce_currency() == 'HKD' ) {
		unset( $available_gateways['itez'] );
	} else if ( WC()->cart->total < 300 && get_woocommerce_currency() == 'TWD' ) {
		unset( $available_gateways['itez'] );
	}
	
	return $available_gateways;

}