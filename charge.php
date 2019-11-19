<script language="javascript">
function onLoadSubmit() {
	document.frm1.submit();
}
</script>
<?php

include("vendor/autoload.php");
# Read the fields that were automatically submitted by beautiful.js
$token = $_POST["startToken"];
$email = $_POST["startEmail"];

$k1 = $_POST['apikey_1'];
$k2 = $_POST['apikey_2'];

$api_keys = array(
  //  "secret_key" => "test_sec_k_16dc38ad730d6ba806a92",
    // "open_key"   => "test_open_k_c3f462a1e8277114c1da"
		"secret_key" => $k2,
		"open_key"   => $k1
);

$amount_in_cents = $_POST['amount_1'];
$currency = $_POST['currency_1'];
$customer_email = $_POST['cus_1'];
$redirect_url = $_POST['r_url'];
$notify_url = $_POST['notify'];
# Setup the Start object with your private API key
Start::setApiKey($api_keys["secret_key"]);

# Process the charge
try {
    $charge = Start_Charge::create(array(
        "amount"      => $amount_in_cents,
        "currency"    => $currency,
        "card"        => $token,
        "email"       => $email,
        "ip"          => $_SERVER["REMOTE_ADDR"],
        "description" => "Charge Description"
    ));
    ?>
    <html>
    <body onload="onLoadSubmit()">
    <form action="<?php echo $notify_url; ?>" method="post" name="frm1" >
      <input type="hidden" name="status" value ="success">
    </form>
  </body>
  </html>

    <?


} catch (Start_Error $e) {
    $error_code = $e->getErrorCode();
    $error_message = $e->getMessage();

    /* depending on $error_code we can show different messages */
    if ($error_code === "card_declined") {
        echo "<h1>Charge was declined</h1>";
    } else {
        echo "<h1>Charge was not processed</h1>";
    }
    echo "<p>".$error_message."</p>";
}

?>
