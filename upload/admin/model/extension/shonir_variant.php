<?php
class ModelExtensionShonirVariant extends Model {
	public function CreateShonirVariantTable() {
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "shonir_variant` (`shonir_variant_id` int(11) NOT NULL AUTO_INCREMENT,`sort_order` int(3) NOT NULL DEFAULT '0',`status` tinyint(1) NOT NULL DEFAULT '1',`image_width` varchar(50) NOT NULL,`image_height` varchar(50) NOT NULL,PRIMARY KEY (`shonir_variant_id`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "shonir_variant_description` (`shonir_variant_id` int(11) NOT NULL,`language_id` int(11) NOT NULL,`title` varchar(64) NOT NULL,`tag` varchar(64) NOT NULL,PRIMARY KEY (`shonir_variant_id`,`language_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8");

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "shonir_variant_product` (`shonir_variant_id` int(11) NOT NULL,`product_id` int(11) NOT NULL,PRIMARY KEY (`shonir_variant_id`,`product_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8");
	}

	public function addShonirVariant($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "shonir_variant SET sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', image_width = '" . $this->db->escape($data['image_width']) . "', image_height = '" . $this->db->escape($data['image_height']) . "'");

		$shonir_variant_id = $this->db->getLastId();

		foreach ($data['shonir_variant_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "shonir_variant_description SET shonir_variant_id = '" . (int)$shonir_variant_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', tag = '" . $this->db->escape($value['tag']) . "'");
		}

		if (isset($data['shonir_variant_product'])) {
			foreach ($data['shonir_variant_product'] as $product_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "shonir_variant_product SET shonir_variant_id = '" . (int)$shonir_variant_id . "', product_id = '" . (int)$product_id . "'");
			}
		}

		return $shonir_variant_id;
	}

	public function editShonirVariant($shonir_variant_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "shonir_variant SET sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', image_width = '" . $this->db->escape($data['image_width']) . "', image_height = '" . $this->db->escape($data['image_height']) . "' WHERE shonir_variant_id = '" . (int)$shonir_variant_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "shonir_variant_description WHERE shonir_variant_id = '" . (int)$shonir_variant_id . "'");

		foreach ($data['shonir_variant_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "shonir_variant_description SET shonir_variant_id = '" . (int)$shonir_variant_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', tag = '" . $this->db->escape($value['tag']) . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "shonir_variant_product WHERE shonir_variant_id = '" . (int)$shonir_variant_id . "'");

		if (isset($data['shonir_variant_product'])) {
			foreach ($data['shonir_variant_product'] as $product_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "shonir_variant_product SET shonir_variant_id = '" . (int)$shonir_variant_id . "', product_id = '" . (int)$product_id . "'");
			}
		}
	}

	public function deleteShonirVariant($shonir_variant_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "shonir_variant WHERE shonir_variant_id = '" . (int)$shonir_variant_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "shonir_variant_description WHERE shonir_variant_id = '" . (int)$shonir_variant_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "shonir_variant_product WHERE shonir_variant_id = '" . (int)$shonir_variant_id . "'");
	}

	public function getShonirVariant($shonir_variant_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "shonir_variant WHERE shonir_variant_id = '" . (int)$shonir_variant_id . "'");

		return $query->row;
	}

	public function getShonirVariants($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "shonir_variant sv LEFT JOIN " . DB_PREFIX . "shonir_variant_description svd ON (sv.shonir_variant_id = svd.shonir_variant_id) WHERE svd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		$sort_data = array(
			'svd.title',
			'svd.title',
			'sv.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY svd.title";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getShonirVariantDescriptions($shonir_variant_id) {
		$shonir_variant_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "shonir_variant_description WHERE shonir_variant_id = '" . (int)$shonir_variant_id . "'");

		foreach ($query->rows as $result) {
			$shonir_variant_description_data[$result['language_id']] = array(
				'title'            => $result['title'],
				'tag'            => $result['tag'],
			);
		}

		return $shonir_variant_description_data;
	}

	public function getTotalShonirVariants() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "shonir_variant");

		return $query->row['total'];
	}

	public function getShonirVariantProducts($shonir_variant_id) {
		$product_Shonir_Variant_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "shonir_variant_product WHERE shonir_variant_id = '" . (int)$shonir_variant_id . "'");

		foreach ($query->rows as $result) {
			$product_Shonir_Variant_data[] = $result['product_id'];
		}

		return $product_Shonir_Variant_data;
	}
}