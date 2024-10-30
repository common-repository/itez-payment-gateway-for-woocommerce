<?php
/**
 * Checkout Order Receipt Template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/order-receipt.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header( 'shop' );
global $woocommerce;
$order_id = $_GET['order-pay'];
$order = new WC_Order( $order_id );

$amount = $order->get_total();
$amount=$amount*100;

$email = $order->get_billing_email();

$lang = substr( get_bloginfo ( 'language' ), 0, 2 );
?>

<script>
														      const timestamp = new Date().getTime();
														      document.write('\x3Cscript src="https://pay.itez.com/static/main/share/merchant.js?'+timestamp+'">\x3C/script>')
														</script>
											            <?php 

											            // creating parameters and signatures
											                $token = $key1;
											                $secret = $key2;
											                $params = [
											                    'partner_token' => $token,
											                    'partner_operation_id' => $order_id,
											                    'user_login' => $email,
											                    'to_account' => $wallet_pay,
											                    'target_element' => 'widget-container',
											                    'timestamp' => time(),
											                    'from_amount' => $amount,
											                    'from_currency' => $currency_pay,
											                    'lang' => $lang,
											                ];
											                ksort($params);
											                $data = [];
											                foreach ($params as $key => $value) {
											                    $data[] = "$key:$value";
											                }
											                $data = join(";", $data);
											                $hash = hash_hmac('sha512', $data, $secret);
											                $params['signature'] = $hash;
											                $params['onLoaded'] = 'this.loaded';
											                $params['onOperationSuccess'] = 'this.success';
											                $array_json = json_encode($params);
											                $array_json = str_replace('"this.loaded"', 'this.loaded', $array_json);
											                $array_json = str_replace('"this.success"', 'this.success', $array_json);


											            ?>
											            <!-- output a widget in a template -->
											                        <div id="widget-container"></div>
											                        <script type="text/javascript">
											                        	var url = "/checkout/order-received/";
											                            ItezWidget.run(
											                                <?php echo $array_json;  ?>
											                            );

																		function loaded(data){
        																	console.log('widget loaded');
																		}

																		function success(data){
																		    window.location.href = url;
																		}
											                        </script>
<?php
get_footer( 'shop' );

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */

