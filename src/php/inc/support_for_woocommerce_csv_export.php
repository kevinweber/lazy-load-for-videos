<?php
/**
 * When generating CSV exports for WooCommerce, ignore oEmbed metadata.
 * Otherwise this can result in thousands of useless columns being added.
 */

function lazyload_videos_woocommerce_product_export_skip_meta_keys($keys_to_skip, $product) {
    $meta_data = $product->get_meta_data();
    $meta_data_keys = array_column($meta_data, 'key');

    $lazyload_videos_keys_to_skip = array_filter($meta_data_keys, function($key) {
        return str_starts_with($key, '_oembed') || str_starts_with($key, 'oembed_');
    });

    $all_keys_to_skip = array_merge($lazyload_videos_keys_to_skip, $keys_to_skip);

    return $all_keys_to_skip;
}

// https://github.com/woocommerce/woocommerce/blob/fe81a4cf27601473ad5c394a4f0124c785aaa4e6/plugins/woocommerce/includes/export/class-wc-product-csv-exporter.php#L724
add_filter('woocommerce_product_export_skip_meta_keys',  'lazyload_videos_woocommerce_product_export_skip_meta_keys', 10, 4); 