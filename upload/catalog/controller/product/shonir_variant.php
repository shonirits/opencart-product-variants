<?php
class ControllerProductShonirVariant extends Controller {
	public function index() {
		$this->load->language('catalog/shonir_variant');

		$this->load->model('catalog/shonir_variant');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		$data['base_product_id'] = $this->request->get['product_id'];
		$get_shonir_variants = $this->model_catalog_shonir_variant->getShonirVariants($data['base_product_id']);

		$shonir_variants = array();
		foreach($get_shonir_variants as $get_shonir_variant) {
		$shonir_variant = $this->model_catalog_shonir_variant->getShonirVariant($get_shonir_variant['shonir_variant_id']);		
		$shonir_variant_products = $this->model_catalog_shonir_variant->getShonirVariantProducts($get_shonir_variant['shonir_variant_id']);
		$product_data = array();
		foreach ($shonir_variant_products as $shonir_variant_product) {
			if($shonir_variant_product['product_id']<>$data['base_product_id']){
			$product_info = $this->model_catalog_product->getProduct($shonir_variant_product['product_id']);
			if($product_info) {
				if ($product_info['image']) {
					$p_thumb = $this->model_tool_image->resize($product_info['image'], $shonir_variant[0]['image_width'], $shonir_variant[0]['image_height']);
				} else {
					$p_thumb = '';
				}
				if ($product_info['quantity'] <= 0) {
					$stock = $product_info['stock_status'];
				} elseif ($this->config->get('config_stock_display')) {
					$stock = $product_info['quantity'];
				} else {
					$stock = $this->language->get('text_instock');
				}
				$product_data[] = array(
					'product_id'				=> $product_info['product_id'],
					'thumb'						=> $p_thumb,
					'stock'						=> htmlspecialchars($stock, ENT_QUOTES, 'UTF-8'),
					'name'						=> htmlspecialchars($product_info['name'], ENT_QUOTES, 'UTF-8'),
					'model'						=> $product_info['model'],
					'href'						=> $this->url->link('product/product', 'product_id='. $product_info['product_id'], true),
				);
			}
		}
		}
		$shonir_variant[0]['products'] = $product_data;		
		$shonir_variants[] = $shonir_variant;
		}

		$data['shonir_variants'] = $shonir_variants;
		if($data['shonir_variants']) {
			return $this->load->view('product/shonir_variant', $data);
		}
	}
}