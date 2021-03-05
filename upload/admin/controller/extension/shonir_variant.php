<?php
class ControllerExtensionShonirVariant extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/shonir_variant');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/shonir_variant');

		$this->model_extension_shonir_variant->CreateShonirVariantTable();

		$this->getList();
	}

	public function add() {
		$this->load->language('extension/shonir_variant');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/shonir_variant');

		$this->load->model('catalog/product');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_shonir_variant->addShonirVariant($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('extension/shonir_variant', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('extension/shonir_variant');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/shonir_variant');

		$this->load->model('catalog/product');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

			$this->model_extension_shonir_variant->editShonirVariant($this->request->get['shonir_variant_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('extension/shonir_variant', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('extension/shonir_variant');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/shonir_variant');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $shonir_variant_id) {
				$this->model_extension_shonir_variant->deleteShonirVariant($shonir_variant_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('extension/shonir_variant', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'sv.sort_order';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/shonir_variant', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('extension/shonir_variant/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('extension/shonir_variant/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['shonir_variants'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$shonir_variant_total = $this->model_extension_shonir_variant->getTotalShonirVariants();

		$results = $this->model_extension_shonir_variant->getShonirVariants($filter_data);

		foreach ($results as $result) {
			$data['shonir_variants'][] = array(
				'shonir_variant_id' => $result['shonir_variant_id'],
				'title'          => $result['title'],
				'tag'          => $result['tag'],
				'sort_order'     => $result['sort_order'],
				'edit'           => $this->url->link('extension/shonir_variant/edit', 'user_token=' . $this->session->data['user_token'] . '&shonir_variant_id=' . $result['shonir_variant_id'] . $url, true)
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['column_title'] = $this->language->get('column_title');
		$data['column_sort_order'] = $this->language->get('column_sort_order');
		$data['column_action'] = $this->language->get('column_action');

		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_title'] = $this->url->link('extension/shonir_variant', 'user_token=' . $this->session->data['user_token'] . '&sort=svd.title' . $url, true);
		$data['sort_tag'] = $this->url->link('extension/shonir_variant', 'user_token=' . $this->session->data['user_token'] . '&sort=svd.tag' . $url, true);
		$data['sort_status'] = $this->url->link('extension/shonir_variant', 'user_token=' . $this->session->data['user_token'] . '&sort=sv.status' . $url, true);
		$data['sort_sort_order'] = $this->url->link('extension/shonir_variant', 'user_token=' . $this->session->data['user_token'] . '&sort=sv.sort_order' . $url, true);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $shonir_variant_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('extension/shonir_variant', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($shonir_variant_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($shonir_variant_total - $this->config->get('config_limit_admin'))) ? $shonir_variant_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $shonir_variant_total, ceil($shonir_variant_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->config->set('template_engine', 'template');
		$this->response->setOutput($this->load->view('extension/shonir_variant_list', $data));
	}

	protected function getForm() {
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_form'] = !isset($this->request->get['shonir_variant_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_default'] = $this->language->get('text_default');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['entry_title'] = $this->language->get('entry_title');
		$data['entry_tag'] = $this->language->get('entry_tag');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$data['entry_products'] = $this->language->get('entry_products');
		$data['entry_width'] = $this->language->get('entry_width');
		$data['entry_height'] = $this->language->get('entry_height');
		$data['entry_image_size'] = $this->language->get('entry_image_size');

		$data['help_products'] = $this->language->get('help_products');

		$data['button_remove'] = $this->language->get('button_remove');
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		$data['tab_general'] = $this->language->get('tab_general');
		$data['tab_products'] = $this->language->get('tab_products');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['title'])) {
			$data['error_title'] = $this->error['title'];
		} else {
			$data['error_title'] = array();
		}

		if (isset($this->error['tag'])) {
			$data['error_tag'] = $this->error['tag'];
		} else {
			$data['error_tag'] = array();
		}

		if (isset($this->error['image_size'])) {
			$data['error_image_size'] = $this->error['image_size'];
		} else {
			$data['error_image_size'] = '';
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/shonir_variant', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['shonir_variant_id'])) {
			$data['action'] = $this->url->link('extension/shonir_variant/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('extension/shonir_variant/edit', 'user_token=' . $this->session->data['user_token'] . '&shonir_variant_id=' . $this->request->get['shonir_variant_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('extension/shonir_variant', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['shonir_variant_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$shonir_variant_info = $this->model_extension_shonir_variant->getShonirVariant($this->request->get['shonir_variant_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['shonir_variant_description'])) {
			$data['shonir_variant_description'] = $this->request->post['shonir_variant_description'];
		} elseif (isset($this->request->get['shonir_variant_id'])) {
			$data['shonir_variant_description'] = $this->model_extension_shonir_variant->getShonirVariantDescriptions($this->request->get['shonir_variant_id']);
		} else {
			$data['shonir_variant_description'] = array();
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($shonir_variant_info)) {
			$data['status'] = $shonir_variant_info['status'];
		} else {
			$data['status'] = true;
		}

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($shonir_variant_info)) {
			$data['sort_order'] = $shonir_variant_info['sort_order'];
		} else {
			$data['sort_order'] = true;
		}

		if (isset($this->request->post['image_width'])) {
			$data['image_width'] = $this->request->post['image_width'];
		} elseif (!empty($shonir_variant_info)) {
			$data['image_width'] = $shonir_variant_info['image_width'];
		} else {
			$data['image_width'] = 100;
		}

		if (isset($this->request->post['image_height'])) {
			$data['image_height'] = $this->request->post['image_height'];
		} elseif (!empty($shonir_variant_info)) {
			$data['image_height'] = $shonir_variant_info['image_height'];
		} else {
			$data['image_height'] = 100;
		}

		if (isset($this->request->post['shonir_variant_product'])) {
			$shonir_variant_products = $this->request->post['shonir_variant_product'];
		} elseif (isset($this->request->get['shonir_variant_id'])) {
			$shonir_variant_products = $this->model_extension_shonir_variant->getShonirVariantProducts($this->request->get['shonir_variant_id']);
		} else {
			$shonir_variant_products = array();
		}

		$data['shonir_variant_products'] = array();

		foreach ($shonir_variant_products as $product_id) {
			$shonirvariant_product_info = $this->model_catalog_product->getProduct($product_id);

			if ($shonirvariant_product_info) {
				$data['shonir_variant_products'][] = array(
					'product_id' => $shonirvariant_product_info['product_id'],
					'name'       => $shonirvariant_product_info['name']
				);
			}
		}

		$this->load->model('tool/image');
		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->config->set('template_engine', 'template');
		$this->response->setOutput($this->load->view('extension/shonir_variant_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/shonir_variant')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['shonir_variant_description'] as $language_id => $value) {
			if ((utf8_strlen($value['title']) < 3) || (utf8_strlen($value['title']) > 255)) {
				$this->error['title'][$language_id] = $this->language->get('error_title');
			}
			if ((utf8_strlen($value['tag']) < 3) || (utf8_strlen($value['tag']) > 255)) {
				$this->error['tag'][$language_id] = $this->language->get('error_tag');
			}
		}

		if (!$this->request->post['image_width'] || !$this->request->post['image_height']) {
			$this->error['image_size'] = $this->language->get('error_image_size');
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'extension/shonir_variant')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}