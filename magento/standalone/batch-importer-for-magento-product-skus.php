<?php

/*
 * Batch importer for Magento Product SKUs
 * This will replace the current SKU with a new/ SKU from a CSV file.
 * https://keith.kg/batch-importer-for-magento-product-skus
 * One product per line format: "oldsku","newsku"
 */

ini_set('error_reporting', E_ALL);

// Location of Mage.php relative to current script
include_once 'app/Mage.php';
Mage::app();

// Location of CSV relative to file system root
$updates_file="/magento/var/import/sku2sku.csv";
$sku_entry=array();
echo realpath(dirname(__FILE__));
$updates_handle=fopen($updates_file, 'r');
echo "1 ";
if($updates_handle) {
	echo "2 ";
	while($sku_entry=fgetcsv($updates_handle, 1000, ",")) {
		$old_sku=$sku_entry[0];
		$new_sku=$sku_entry[1];
		echo "Updating ".$old_sku." to ".$new_sku." - ";
		try {
			$get_item = Mage::getModel('catalog/product')->loadByAttribute('sku', $old_sku);
            if ($get_item) {
	            $get_item->setSku($new_sku)->save();
                echo "successful";
            } else {
	            echo "item not found";
            }
        } catch (Exception $e) {
			echo "Cannot retrieve products from Magento: ".$e->getMessage()."
";
            return;
        }
	}
}
fclose($updates_handle);