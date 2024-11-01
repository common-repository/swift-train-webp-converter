<?php
	/**
	 * Admin file for the Swift Train Webp Converter plugin.
	 */

// Prevent direct access to this file
	if (!defined('ABSPATH')) exit;

	/**
	 * The admin class for the Swift Train Webp Converter plugin.
	 */
	class Swift_Train_Webp_Converter_Admin
	{
		/** @var int */
		private $deleteSource = 0;

		/** @var int */
		private $quality = 60;

		/** @var array */
		private $sourceFormats = ['jpg', 'png'];

		/**
		 * Initializes the admin class.
		 */
		public function __construct()
		{
			add_action('admin_menu', [$this, 'registerSettingsPage']);
			add_action('wp_ajax_swift_train_webp_converter_save_settings', [$this, 'saveSettings']);
		}

		/**
		 * Get plugin settings
		 *
		 * @return array
		 */
		public function getSettings(): array
		{
			$settings = get_option('swift_train_webp_converter_settings');

			// Default value for delete_source_file
			if (!isset($settings['delete_source_file'])) {
				$settings['delete_source_file'] = $this->deleteSource;
			}

			// Default value for quality
			if (!isset($settings['quality'])) {
				$settings['quality'] = $this->quality;
			}

			// Default value for source_format
			if (!isset($settings['source_format'])) {
				$settings['source_format'] = $this->sourceFormats;
			}

			return $settings;
		}

		/**
		 * Registers the plugin's settings page.
		 */
		public function registerSettingsPage()
		{
			add_submenu_page(
				'options-general.php',
				SWIFT_TRAIN_WEBP_CONVERTER_NAME,
				SWIFT_TRAIN_WEBP_CONVERTER_NAME,
				'manage_options',
				SWIFT_TRAIN_WEBP_CONVERTER_SLUG,
				[$this, 'renderSettingsPage']
			);

			add_filter('plugin_action_links_' . SWIFT_TRAIN_WEBP_CONVERTER_BASENAME, function ($links) {
				$newLinks = [
					'<a href="' . get_admin_url(null, 'options-general.php?page=' . SWIFT_TRAIN_WEBP_CONVERTER_SLUG) . '">' . __('Settings', SWIFT_TRAIN_WEBP_CONVERTER_SLUG) . '</a>',
				];
				foreach ($links as $link) {
					$newLinks[] = $link;
				}

				return $newLinks;
			});
		}

		/**
		 * Load the template file
		 */
		public function renderSettingsPage()
		{
			$formats = $this->sourceFormats;
			$quality = $this->quality;

			// Load the saved settings or use default values
			$settings = $this->getSettings();

			// Load the template file
			$template_path = plugin_dir_path(dirname(__FILE__)) . 'templates/settings-page.php';

			// Load the stylesheet
			wp_enqueue_style('swift-train-webp-converter-settings', plugin_dir_url(dirname(__FILE__)) . 'css/settings.css');

			// Include the template file
			include $template_path;
		}

		/**
		 * Save settings
		 */
		public function saveSettings()
		{
			// Get the posted data
			$settings = $_POST;

			// Check if the source format values are valid
			$invalidFormats = array_diff($settings['source_format'], $this->sourceFormats);
			if (!isset($settings['source_format']) || !empty($invalidFormats)) {
				wp_send_json_error(__('Invalid source format values.', SWIFT_TRAIN_WEBP_CONVERTER_SLUG));
				return;
			}

			// Check if the quality setting is an integer and falls within the allowed range
			if (is_numeric($settings['conversion_quality']) < 1 || intval($settings['conversion_quality']) > 100) {
				wp_send_json_error(__('Invalid quality setting.', SWIFT_TRAIN_WEBP_CONVERTER_SLUG));
				return;
			}

			// Save the data
			update_option('swift_train_webp_converter_settings', [
				'source_format'        => $settings['source_format'],
				'quality'              => $settings['conversion_quality'],
				'delete_source_file'   => $settings['delete_source_file'] == 1 ? 1 : 0,
			]);

			// Return a success response
			wp_send_json_success();
		}
	}
