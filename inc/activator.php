<?php

	/**
	 * Swift_Train_Webp_Converter_Activator
	 */
	class Swift_Train_Webp_Converter_Activator {

		/** @var array */
		const REQUIRED_EXTENSIONS = [
			'exif',
		];

		/** @var string  */
		const TABLE_NAME = 'stwc_attachment_files';

		/**
		 * Activate
		 */
		public static function activate() {
			// Check if extensions are loaded
			foreach (self::REQUIRED_EXTENSIONS as $extension) {
				if (!extension_loaded($extension)) {
					wp_die(sprintf(
						       __('The Swift Train WebP Converter plugin requires the "%s" extension to be enabled on your server. Please enable it and try again.', SWIFT_TRAIN_WEBP_CONVERTER_SLUG),
						       $extension
					       ) . '<div style="padding-top: 1em;"><a href="' . esc_url(admin_url('plugins.php')) . '">&laquo; ' . __('Back to Plugins') . '</a></div>');
				}
			}

			global $wpdb;

			// Check if table exists and create if not
			$table_name = self::TABLE_NAME;

			if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
				$charsetCollate = $wpdb->get_charset_collate();
				$sql = "CREATE TABLE $table_name (
	                id BIGINT(20) NOT NULL AUTO_INCREMENT,
	                attachment_id BIGINT(20) NOT NULL,
	                file_path VARCHAR(1000) NOT NULL,
	                PRIMARY KEY (id)
	            ) $charsetCollate;";

				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				dbDelta($sql);
			}
		}
	}

	// Register the activation hook
	register_activation_hook(__FILE__, ['Swift_Train_Webp_Converter_Activator', 'activate']);
