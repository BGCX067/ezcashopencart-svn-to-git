<?php
class ControllerPaymentEzonline extends Controller {
	protected function index() {
		$this->language->load('payment/ez_online');
		
		$this->data['text_testmode'] = $this->language->get('text_testmode');		
    	
		$this->data['button_confirm'] = $this->language->get('button_confirm');

		$this->data['testmode'] = $this->config->get('ez_online_test');
		
		if (!$this->config->get('ez_online_test')) {
    		$this->data['action'] = "https://ipg.dialog.lk/ezCashIPGExtranet/servlet_sentinal";
  		} else {
			$this->data['action'] = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
		}

		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		if ($order_info) {

			$description="";
			
			foreach ($this->cart->getProducts() as $product) {
				$description=$description.", ".$product['name'];
			}	

			
			//$this->data['transactionAmount'] = $order_info['total'];
			//$this->data['merchantCode'] = $this->config->get('ez_online_email');
			//$this->data['transactionId'] = $this->session->data['order_id'];
			$sensitiveData =$this->config->get('ez_online_email').'|AH_'.$this->session->data['order_id'].'|'.$order_info['total'].'|'.$this->url->link('payment/ez_online/callback');
			$publicKey = <<<EOD
-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCW8KV72IMdhEuuEks4FXTiLU2o
bIpTNIpqhjgiUhtjW4Si8cKLoT7RThyOvUadsgYWejLg2i0BVz+QC6F7pilEfaVS
L/UgGNeNd/m5o/VoX9+caAIyu/n8gBL5JX6asxhjH3FtvCRkT+AgtTY1Kpjb1Btp
1m3mtqHh6+fsIlpH/wIDAQAB
-----END PUBLIC KEY-----
EOD;
			$encrypted = '';
			if (!openssl_public_encrypt($sensitiveData, $encrypted, $publicKey))
			die('Failed to encrypt data');
			$this->data['invoice'] =base64_encode($encrypted);
			//$this->data['custom'] = $this->session->data['order_id'];
			//$this->data['returnUrl'] = $this->url->link('payment/ez_online/callback', '', 'SSL');
			
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/ez_online.tpl')) {
				$this->template = $this->config->get('config_template') . '/template/payment/ez_online.tpl';
			} else {
				$this->template = 'default/template/payment/ez_online.tpl';
			}
	
			$this->render();
		}
	}
	
	public function callback() {
	

		if (isset($this->session->data['order_id'])) {
			$order_id = $this->session->data['order_id'];;
		} else {
			$order_id = 0;
		}		
		//echo "order id".$order_id;
		$this->load->model('checkout/order');
				
		$order_info = $this->model_checkout_order->getOrder($order_id);
		
		if ($order_info) {
			$request = 'cmd=_notify-validate';
		
			foreach ($this->request->post as $key => $value) {
				$request .= '&' . $key . '=' . urlencode(html_entity_decode($value, ENT_QUOTES, 'UTF-8'));
			}
			
			if (!$this->config->get('ez_online_test')) {
				$curl = curl_init('https://ipg.dialog.lk/ezCashIPGExtranet/servlet_sentinal');
			} else {
				$curl = curl_init('https://ipg.dialog.lk/ezCashIPGExtranet/servlet_sentinal');
			}

			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_TIMEOUT, 30);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
					
			$response = curl_exec($curl);
			
			if (!$response) {
				$this->log->write('ez_online :: CURL failed ' . curl_error($curl) . '(' . curl_errno($curl) . ')');
			}
					
			if ($this->config->get('ez_online_debug')) {
				$this->log->write('ez_online :: IPN REQUEST: ' . $request);
				$this->log->write('ez_online :: IPN RESPONSE: ' . $response);
			}
			
				$str =$this->request->post['merchantReciept'];
$privateKey = <<<EOD
-----BEGIN PRIVATE KEY-----
MIICdgIBADANBgkqhkiG9w0BAQEFAASCAmAwggJcAgEAAoGBAJuIUgSzNuWm3US8
0brZr/5cMSPue9f0IwUrEhka1gLlC4uQon6QjQem4TWQ8anoMKYwfYgRnCGQsbrT
KwOApwTA4Bt6dg9jKXlIE6rXqqO6g2C/uD+G2p+W4k0ZI1isuqqjjkup5ZPkNaeW
R9/961Qx3CyrWDk6n0OkzDJ6UNzLAgMBAAECgYEAh+/dv73jfVUaj7l4lZct+2MY
kA8grt7yvNGoP8j0xBLsxE7ltzkgClARBoBot9f4rUg0b3j0vWF59ZAbSDRpxJ2U
BfWEtlXWvN1V051KnKaOqE8TOkGK0PVWcc6P0JhPrbmOu9hhAN3dMu+jd7ABFKgC
4b8EIlHA8bl8po8gwAECQQDliMBTAzzyhB55FMW/pVGq9TBo2oXQsyNOjEO+rZNJ
zIwJzFrFhvuvFj7/7FekDAKmWgqpuOIk0NSYfHCR54FLAkEArXc7pdPgn386ikOc
Nn3Eils1WuP5+evoZw01he4NSZ1uXNkoNTAk8OmPJPz3PrtB6l3DUh1U/DEZjIiI
7z5igQJAFXvFNH/bFn/TMlYFZDie+jdUvpulZrE9nr52IMSyQngIq2obHN3TdMHK
R73hPhN5tAQ9d0E8uWFqZJNRHfbjHQJASY7pNV3Ov/QE0ALxqE3W3VDmJD/OjkOS
jriUPNIAwnnHBgp0OXHMCHkSYX4AHpLr1cWjARw9IKB1lBmF7+YFgQJAFqUgYj11
ioyuSf/CSotPIC7YyNEnr+TK2Ym0N/EWzqNXoOCDxDTgoWLQxM3Nfr65tWtV2097
BjCbFfbui/IyUw==
-----END PRIVATE KEY-----
EOD;

$decrypted='';
$encrypted = base64_decode($str); // decode the encrypted query string
if (!openssl_private_decrypt($encrypted, $decrypted, $privateKey))
die('Failed to decrypt data');
$receiveddata=explode("|", $decrypted);	
		//echo $receiveddata[1];
			
			if (isset($receiveddata[1])) {
				
				$order_status_id = $this->config->get('config_order_status_id');
				//echo $receiveddata[0];
				//echo $receiveddata[1];
				//echo $receiveddata[2];
			//	echo $receiveddata[3];
				//echo $receiveddata[5];

				switch($receiveddata[1]) {
					case '2':
						if ((strtolower($receiveddata[4]) == strtolower($this->config->get('ez_online_email'))) && (floatval($receiveddata[3]) == $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false))) {
							$order_status_id = $this->config->get('ez_online_completed_status_id');
						} else {
							$this->log->write('ez_online :: RECEIVER EMAIL MISMATCH! ' . strtolower($receiveddata[4]));
						}
						break;
					case '3':
						$order_status_id = $this->config->get('ez_online_failed_status_id');
						break;
					case '4':
						$order_status_id = $this->config->get('ez_online_failed_status_id');
						break;
					case '5':
						$order_status_id = $this->config->get('ez_online_expired_status_id');
						break;
					case '6':
						$order_status_id = $this->config->get('ez_online_expired_status_id');
						break;
					case '7':
						$order_status_id = $this->config->get('ez_online_pending_status_id');
						break;
					case '8':
						$order_status_id = $this->config->get('ez_online_processed_status_id');
						break;
					case '9':
						$order_status_id = $this->config->get('ez_online_refunded_status_id');
						break;
					case '10':
						$order_status_id = $this->config->get('ez_online_reversed_status_id');
						break;
					case '11':
						$order_status_id = $this->config->get('ez_online_failed_status_id');
						break;
					case '12':
						$order_status_id = $this->config->get('ez_online_failed_status_id');
						break;
					case '13':
						$order_status_id = $this->config->get('ez_online_failed_status_id');
						break;
					case '14':
						$order_status_id = $this->config->get('ez_online_failed_status_id');
						break;
					case '15':
						$order_status_id = $this->config->get('ez_online_failed_status_id');
						break;
					default:
						$order_status_id = $this->config->get('ez_online_voided_status_id');
						break;								
				}
				
				if ($receiveddata[1]=='2') {
					if(isset($receiveddata[5])){
						$this->model_checkout_order->confirm($order_id, $order_status_id,"Wallet Ref=".$receiveddata[5]."-".$receiveddata[2],true);
					}

					$this->redirect($this->url->link('checkout/success'));
				} else {
				$this->model_checkout_order->update($order_id, $order_status_id,"Status=".$receiveddata[2],false);
				$this->redirect($this->url->link('checkout/checkout'));
				}
					
			} else {
				//$this->model_checkout_order->confirm($order_id, $this->config->get('config_order_status_id'));
				$this->redirect($this->url->link('checkout/checkout'));
			}
			
			curl_close($curl);
		}	
	}
}
?>