
<html>
<head>
<script>
function target_popup(form) {
    window.open('', 'formpopup', 'width=600,height=600,resizeable,scrollbars');
    form.target = 'formpopup';
}
</script>
</head>
</html>
<?php

// This Payfort gateway with VikEvents Components
// Developed by : Hazem Abouseafa
// https://mostaql.com/u/hazemabouseafa

class VikEventsPayment {
  private $validation;
	private $order_info;
	private $params;

	public static function getAdminParameters() {
		$logo_img = JURI::root().'administrator/components/com_vikevents/payments/payfort_logo_en.png';
		return array(
			'logo' => array(
				'label' => '',
				'type' => 'custom',
				'html' => '<img src="'.$logo_img.'"/>'
			),
			'apikey1' => array(
				'label' => 'API KEY (OPEN)',
				'type' => 'text'
			),
      'apikey2' => array(
				'label' => 'API KEY (SECRET)',
				'type' => 'text'
			),
			'testmode' => array(
				'label' => 'Test Mode',
				'type' => 'select',
				'options' => array('Yes', 'No'),
			),
		);
	}

	public function __construct($order, $params=array()) {
		$this->order_info = $order;
		$this->params = $params;
	}
	public function showPayment() {

    $merchant_id = $this->params['merchantid'];
    $access_code = $this->params['accesscode'];
    $phrase =  $this->params['phrase'];
    $amount = $this->order_info['total_to_pay']."00";
    $api_key1 = $this->params['apikey1'];
    $api_key2 = $this->params['apikey2'];
		$action_url = "https://sbcheckout.payfort.com";
		if( $this->params['testmode'] == 'Yes' ) {
					$action_url = "https://sbcheckout.payfort.com/FortAPI/paymentPage";
				}
				$service_command = "TOKENIZATION";
				$merchant_reference = "Test";
				$language = "en";
				$currency = "QAR";
				$customer_email ="test@payfort.com";

include("config.php");

      ?>

      <form action="charge.php" method="post">
          <script src="https://beautiful.start.payfort.com/checkout.js"
              data-key="<?php echo $api_keys['open_key']; ?>"
              data-currency="<?php echo $currency ?>"
              data-amount="<?php echo $amount ?>"
              data-email="<?php echo $customer_email ?>">
        </script>
        <input type="hidden" name="amount_1" value="<?php echo $amount; ?>">
        <input type="hidden" name="currency_1" value="<?php echo $currency; ?>">
        <input type="hidden" name="cus_1" value="<?php echo $customer_email; ?>">
        <input type="hidden" name="notify" value="<?php echo $this->order_info['notify_url']; ?>">
        <input type="hidden" name="apikey_1" value="<?php echo $api_key1; ?>">
        <input type="hidden" name="apikey_2" value="<?php echo $api_key2; ?>">
      </form>


      <?

	}

	public function validatePayment() {
		$array_result = array();
			$array_result['verified'] = 0;
			$array_result['tot_paid'] = ''; /** This value will be stored in the DB */

			/** In case of error the logs will be sent via email to the admin */
			$array_result['log'] = '';

			$status = $_POST['status'];
			/** Process your gateway response here */
			if($status == 'success') {
				$array_result['verified'] = 1;
        $this->validation = 1;
				/** Set a value for $array_result['tot_paid'] */
				$array_result['tot_paid'] = $_POST['amount'];
			} else {
				$array_result['log'] = "Transaction Error!\n".$_POST['error_msg'];
			}

			/** Return the array result to VikEvents */
			return $array_result;

	}

	public function afterValidation ($esit = 0) {
    $esit = $this->validation;
		$mainframe = JFactory::getApplication();
		//URL to order details page
		$redirect_url = 'index.php?option=com_vikevents&task=orders&oid='.$this->order_info['id'].'&scode='.$this->order_info['scode'];

		if($esit < 1) {
			JError::raiseWarning('', 'The payment was not verified, please try again.');
			$mainframe->redirect($redirect_url);
		} else {
			$mainframe->enqueueMessage('Thank you! The payment was verified successfully.');
			$mainframe->redirect($redirect_url);
		}

		exit;
		//No page rendering
	}
}
?>
