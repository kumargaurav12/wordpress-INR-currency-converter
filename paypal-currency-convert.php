<?php
/*
=============================================
====== PayPal Currency Convert ======
=============================================
 */
add_filter('woocommerce_paypal_supported_currencies', 'pbte_add_bgn_paypal_valid_currency');

function pbte_add_bgn_paypal_valid_currency($currencies) {
	array_push($currencies, 'INR');
	return $currencies;
}

add_filter('woocommerce_paypal_args', 'pbte_convert_bgn_to_eur');

function pbte_convert_bgn_to_eur($paypal_args) {
	if ($paypal_args['currency_code'] == 'INR') {
		//$convert_rate = get_option('pbte_eur_to_bgn_rate'); //set the converting rate
		// $convert_rate = 1.54;
		$convert_rate = currency_converter();
		$paypal_args['currency_code'] = 'USD'; //change BGN to EUR
		$i = 1;

		while (isset($paypal_args['amount_' . $i])) {
			$paypal_args['amount_' . $i] = round($paypal_args['amount_' . $i] / $convert_rate, 2);
			++$i;
		}
	}
	return $paypal_args;
}

// ==============Currency Convert =============
function currency_converter() {
	$url = "http://apilayer.net/api/live?access_key=77b8f2f07bec205f9a3be677f2cdffb1&currencies=INR&source=USD";

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	curl_setopt($ch, CURLOPT_URL, $url);

	$result = file_get_contents($url);
	curl_close($ch);

	$json_data = json_decode($result, true);
	$success = $json_data["success"];
	$new_currency = $json_data["quotes"]["USDINR"];
	if ($success == 1) {
		$dollar = round($new_currency, 2);
	}
	return $dollar;
}
?>
