<?php
class ModelCatalogShonirVariant extends Model {

	public function getShonirVariant($variant_id = '') {

		$extra = ($variant_id)?' and sv.shonir_variant_id='.(int)$variant_id:'';
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "shonir_variant sv LEFT JOIN " . DB_PREFIX . "shonir_variant_description svd ON (sv.shonir_variant_id = svd.shonir_variant_id) WHERE svd.language_id = '". (int)$this->config->get('config_language_id') ."' ".$extra);
		return $query->rows;
	}

	public function getShonirVariants($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "shonir_variant_product WHERE product_id=".(int)$product_id);
		return $query->rows;
	}

	public function getShonirVariantProducts($variant_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "shonir_variant_product WHERE shonir_variant_id=".(int)$variant_id);
		return $query->rows;
	}

}