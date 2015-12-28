<?php
class ControllerPaymentEzonline extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('payment/ez_online');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('ez_online', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_all_zones'] = $this->language->get('text_all_zones');
		$this->data['text_yes'] = $this->language->get('text_yes');
		$this->data['text_no'] = $this->language->get('text_no');
		$this->data['text_authorization'] = $this->language->get('text_authorization');
		$this->data['text_sale'] = $this->language->get('text_sale');

		$this->data['entry_email'] = $this->language->get('entry_email');
		$this->data['entry_test'] = $this->language->get('entry_test');
		$this->data['entry_transaction'] = $this->language->get('entry_transaction');
		$this->data['entry_debug'] = $this->language->get('entry_debug');
		$this->data['entry_total'] = $this->language->get('entry_total');	
		$this->data['entry_canceled_reversal_status'] = $this->language->get('entry_canceled_reversal_status');
		$this->data['entry_completed_status'] = $this->language->get('entry_completed_status');
		$this->data['entry_denied_status'] = $this->language->get('entry_denied_status');
		$this->data['entry_expired_status'] = $this->language->get('entry_expired_status');
		$this->data['entry_failed_status'] = $this->language->get('entry_failed_status');
		$this->data['entry_pending_status'] = $this->language->get('entry_pending_status');
		$this->data['entry_processed_status'] = $this->language->get('entry_processed_status');
		$this->data['entry_refunded_status'] = $this->language->get('entry_refunded_status');
		$this->data['entry_reversed_status'] = $this->language->get('entry_reversed_status');
		$this->data['entry_voided_status'] = $this->language->get('entry_voided_status');
		$this->data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');

		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

 		if (isset($this->error['email'])) {
			$this->data['error_email'] = $this->error['email'];
		} else {
			$this->data['error_email'] = '';
		}

		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),      		
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_payment'),
			'href'      => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('payment/ez_online', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

		$this->data['action'] = $this->url->link('payment/ez_online', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['ez_online_email'])) {
			$this->data['ez_online_email'] = $this->request->post['ez_online_email'];
		} else {
			$this->data['ez_online_email'] = $this->config->get('ez_online_email');
		}

		if (isset($this->request->post['ez_online_test'])) {
			$this->data['ez_online_test'] = $this->request->post['ez_online_test'];
		} else {
			$this->data['ez_online_test'] = $this->config->get('ez_online_test');
		}

		if (isset($this->request->post['ez_online_transaction'])) {
			$this->data['ez_online_transaction'] = $this->request->post['ez_online_transaction'];
		} else {
			$this->data['ez_online_transaction'] = $this->config->get('ez_online_transaction');
		}

		if (isset($this->request->post['ez_online_debug'])) {
			$this->data['ez_online_debug'] = $this->request->post['ez_online_debug'];
		} else {
			$this->data['ez_online_debug'] = $this->config->get('ez_online_debug');
		}
		
		if (isset($this->request->post['ez_online_total'])) {
			$this->data['ez_online_total'] = $this->request->post['ez_online_total'];
		} else {
			$this->data['ez_online_total'] = $this->config->get('ez_online_total'); 
		} 

		if (isset($this->request->post['ez_online_canceled_reversal_status_id'])) {
			$this->data['ez_online_canceled_reversal_status_id'] = $this->request->post['ez_online_canceled_reversal_status_id'];
		} else {
			$this->data['ez_online_canceled_reversal_status_id'] = $this->config->get('ez_online_canceled_reversal_status_id');
		}
		
		if (isset($this->request->post['ez_online_completed_status_id'])) {
			$this->data['ez_online_completed_status_id'] = $this->request->post['ez_online_completed_status_id'];
		} else {
			$this->data['ez_online_completed_status_id'] = $this->config->get('ez_online_completed_status_id');
		}	
		
		if (isset($this->request->post['ez_online_denied_status_id'])) {
			$this->data['ez_online_denied_status_id'] = $this->request->post['ez_online_denied_status_id'];
		} else {
			$this->data['ez_online_denied_status_id'] = $this->config->get('ez_online_denied_status_id');
		}
		
		if (isset($this->request->post['ez_online_expired_status_id'])) {
			$this->data['ez_online_expired_status_id'] = $this->request->post['ez_online_expired_status_id'];
		} else {
			$this->data['ez_online_expired_status_id'] = $this->config->get('ez_online_expired_status_id');
		}
				
		if (isset($this->request->post['ez_online_failed_status_id'])) {
			$this->data['ez_online_failed_status_id'] = $this->request->post['ez_online_failed_status_id'];
		} else {
			$this->data['ez_online_failed_status_id'] = $this->config->get('ez_online_failed_status_id');
		}	
								
		if (isset($this->request->post['ez_online_pending_status_id'])) {
			$this->data['ez_online_pending_status_id'] = $this->request->post['ez_online_pending_status_id'];
		} else {
			$this->data['ez_online_pending_status_id'] = $this->config->get('ez_online_pending_status_id');
		}
									
		if (isset($this->request->post['ez_online_processed_status_id'])) {
			$this->data['ez_online_processed_status_id'] = $this->request->post['ez_online_processed_status_id'];
		} else {
			$this->data['ez_online_processed_status_id'] = $this->config->get('ez_online_processed_status_id');
		}

		if (isset($this->request->post['ez_online_refunded_status_id'])) {
			$this->data['ez_online_refunded_status_id'] = $this->request->post['ez_online_refunded_status_id'];
		} else {
			$this->data['ez_online_refunded_status_id'] = $this->config->get('ez_online_refunded_status_id');
		}

		if (isset($this->request->post['ez_online_reversed_status_id'])) {
			$this->data['ez_online_reversed_status_id'] = $this->request->post['ez_online_reversed_status_id'];
		} else {
			$this->data['ez_online_reversed_status_id'] = $this->config->get('ez_online_reversed_status_id');
		}

		if (isset($this->request->post['ez_online_voided_status_id'])) {
			$this->data['ez_online_voided_status_id'] = $this->request->post['ez_online_voided_status_id'];
		} else {
			$this->data['ez_online_voided_status_id'] = $this->config->get('ez_online_voided_status_id');
		}

		$this->load->model('localisation/order_status');

		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['ez_online_geo_zone_id'])) {
			$this->data['ez_online_geo_zone_id'] = $this->request->post['ez_online_geo_zone_id'];
		} else {
			$this->data['ez_online_geo_zone_id'] = $this->config->get('ez_online_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['ez_online_status'])) {
			$this->data['ez_online_status'] = $this->request->post['ez_online_status'];
		} else {
			$this->data['ez_online_status'] = $this->config->get('ez_online_status');
		}
		
		if (isset($this->request->post['ez_online_sort_order'])) {
			$this->data['ez_online_sort_order'] = $this->request->post['ez_online_sort_order'];
		} else {
			$this->data['ez_online_sort_order'] = $this->config->get('ez_online_sort_order');
		}

		$this->template = 'payment/ez_online.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/ez_online')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['ez_online_email']) {
			$this->error['email'] = $this->language->get('error_email');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
}
?>