<?php
//
// I feel there are never too many comments in code.  Maybe it's just me.
//
// Tom Donnelly, January 2016. covtom@gmail.com crazytom.com
//
// ------------------------------------------------------------------------------------
// IPN is PayPal's way of securely and reliably informing your website that you've had a transaction.
//
// If you want to offer instant access to digital downloads after payment, then you need to be informed as soon as the payment 
// has completed so that you don't keep your customer waiting when they automatically return to your site.  (You do have 
// Auto-Return set on in your PayPal Seller Preferences - right?   
//  
// "But if I have Auto Return to my 'success.php' page and Payment Data Transfer enabled in Seller Preferences, why do I need IPN?" I hear 
// you yell.  Good point. With these switched on, your client is automatically returned to your site after payment and you get confirmation 
// of the transaction.  But wait, there's more...  
//
// There are two main reasons to use IPN:
//
// 1. Auto-Return uses GET not POST to give you this data and it's easy to spoof
// 2. After completing payment on PayPal, your client may elect not to return to your website, or their connection breaks or a giant worm falls on their head
//
// So a sure-fire way of getting a reliable transaction confirmation in "real-time" from PayPal is via IPN.  In fact after purchase, PayPal
// issues a message saying "you will be returned .. in 10 seconds automatically".  There is a "go back now" button, but it's er.. "sluggish" to give
// time for the IPN to complete before returning the customer to your site.
//
// Paypal IPN calls a program (URI)on your site (which I have called listener.php) with an array of variables about the transaction as POST data.
// All we have to do is acknowledge the notification with an HTTP 200 response, extract the variables they send to us (to record our own confirmation
//  of the transaction) and return the same data back to PayPal via HTTP with the text "cmd=_notify-validate" added in front of the data they sent.
//
// This last bit is to check that PayPal was the sender of the IPN.  PayPal checks that this is data that it sent to us If we get a good response to that, 
// then it's authentic.
//
// How hard can it be?

//
// STEP 1 - be polite and acknowledge PayPal's notification
//
$db = new PDO('sqlite:/var/www/cart.db');
header('HTTP/1.1 200 OK');
//
// STEP 2 - create the response we need to send back to PayPal for them to confirm that it's legit
//

$resp = 'cmd=_notify-validate';
foreach ($_POST as $parm => $var) 
{
	$var = urlencode(stripslashes($var));
	$resp .= "&$parm=$var";
}

// STEP 3 - Extract the data PayPal IPN has sent us, into local variables 

$item_name        = $_POST['item_name1'];
$item_number      = $_POST['item_number'];
$payment_status   = $_POST['payment_status'];
$payment_amount   = $_POST['mc_gross'];
$payment_currency = $_POST['mc_currency'];
$txn_id           = $_POST['txn_id'];
$txn_type         = $_POST['txn_type'];
$receiver_email   = $_POST['receiver_email'];
$payer_email      = $_POST['payer_email'];
$record_id	 	  = $_POST['custom'];
$invoice_id	 	  = $_POST['invoice']; // the id in db
$number_of_items  = $_POST['num_cart_items'];

// Right.. we've pre-pended "cmd=_notify-validate" to the same data that PayPal sent us (I've just shown some of the data PayPal gives us. A complete list
// is on their developer site.  Now we need to send it back to PayPal via HTTP.  To do that, we create a file with the right HTTP headers followed by 
// the data block we just createdand then send the whole bally lot back to PayPal using fsockopen


// STEP 4 - Get the HTTP header into a variable and send back the data we received so that PayPal can confirm it's genuine

$httphead = "POST /cgi-bin/webscr HTTP/1.0\r\n";
$httphead .= "Content-Type: application/x-www-form-urlencoded\r\n";
$httphead .= "Content-Length: " . strlen($resp) . "\r\n";
//$httphead .= "Content-Length: " . strlen($resp) . "\r\n\r\n";
$httphead .= "Host: www.sandbox.paypal.com\r\n";
$httphead .= "Connection: close\r\n\r\n";
 // Now create a ="file handle" for writing to a URL to paypal.com on Port 443 (the IPN port)

$errno ='';
$errstr='';
// ssl://www.paypal.com
$fh = fsockopen ('ssl://www.sandbox.paypal.com', 443, $errno, $errstr, 30);

// STEP 5 - Nearly done.  Now send the data back to PayPal so it can tell us if the IPN notification was genuine
 
if (!$fh) {
 
	// Uh oh. This means that we have not been able to get thru to the PayPal server.  It's an HTTP failure
	//
	// You need to handle this here according to your preferred business logic.  An email, a log message, a trip to the pub..
	$sql = "INSERT INTO TESTINGG (OID, TID) VALUES (NULL, ?)";
	$q = $db->prepare($sql);
	$test = "Arrived here";
	$q->bindParam(1, $test);
	$q->execute();
} 
		   
// Connection opened, so spit back the response and get PayPal's view whether it was an authentic notification		   
		   
else {
	fputs ($fh, $httphead . $resp);
	while (!feof($fh))
	{
		$readresp = fgets ($fh, 1024);
		if (strcmp (trim($readresp), "VERIFIED") == 0) 
		{
			$sql = "INSERT INTO TESTINGG (OID, TID) VALUES (NULL, 'ABC1')";
			$db->exec($sql);

			$order_object = [
				'purchase_units' => array (
					0 => [
						'amount' => [
							'currency_code' => $payment_currency,
							'value' => $payment_amount,
							'breakdown' => [
								'item_total'=> [
									'currency_code' => $payment_currency,
									'value' => $payment_amount
								]

							]
						],
						'items' => array (
							
						)

					]
				)
			];
			for ($i = 1; $i <= $number_of_items; $i++) {
				// create item object
				$item_name = $_POST["item_name".$i];
				$item_quan = $_POST["quantity".$i];
				$item_cost = $_POST["mc_gross".$i];
				$item_object = [
					'name' => $item_name,
					'quantity' => $item_quan,
					'unit_amount' => [
						'currency_code' => $payment_currency,
						'value' => $item_cost
					]
				];
				array_push($order_object['purchase_units'][0]['items'], $item_object);
			}
			$order_object_json = json_encode($order_object);

			$sql = "SELECT * FROM orders WHERE OID=?";
			$q = $db->prepare($sql);
			$q->bindParam(1, $invoice_id);
			$q->execute();
			$res = $q->fetch();
            $salt = $res["SALT"];
			$target_digest = $res["DIGEST"];
			$old_txnid = $res["TXNID"];

			// check txnid, txntype
			if (strcmp($txn_type, "cart") == 0) {
				if (strcmp($old_txnid, "none") == 0) {
					// first time process
					// check digest
					$new_digest = hash_hmac('sha256', $order_object_json, $salt);
					if (strcmp($target_digest, $new_digest)) {
						$sql = "INSERT INTO TESTINGG (OID, TID) VALUES (NULL, 'SAMEEE')";
						$db->exec($sql);

						$sql = "UPDATE orders SET STATUS=?, TXNID=? WHERE OID=?";
						$q = $db->prepare($sql);
						$q->bindParam(1, $payment_status);
						$q->bindParam(2, $txn_id);
						$q->bindParam(3, $invoice_id);
						$q->execute();
					
					}
				}
			}






			$sql = "INSERT INTO TESTINGG (OID, TID) VALUES (NULL, 'ABC2')";
			$db->exec($sql);
		}
 
		else if (strcmp (trim($readresp), "INVALID") == 0) 
		{
			// Man alive!  A hacking attempt?


 			$sql = "INSERT INTO TESTINGG (OID, TID) VALUES (NULL, 'Doe')";
			$db->exec($sql);
		}
			
		
	}
	fclose ($fh);
}
//
//
// STEP 6 - Pour yourself a cold one.
//
//
