<div class="wrap" id="<?= SWIFT_TRAIN_WEBP_CONVERTER_SLUG ?>">
	<h1 class="wp-heading-inline"><?php echo esc_html(get_admin_page_title()); ?></h1>
	<hr class="wp-header-end">
	<div class="section">
		<!--		<h2>General Settings</h2>-->
		<div class="section-content">
			<div class="swift-train-webp-converter-setting-field">
				<label><?php esc_html_e('Source Format', SWIFT_TRAIN_WEBP_CONVERTER_SLUG); ?>:</label>
				<div class="swift-train-webp-converter-checkboxes">
					<?php
					foreach ($formats as $format) {
						$checked = in_array($format, $settings['source_format']) ? 'checked' : '';
						?>
						<div class="swift-train-webp-converter-setting-option">
							<input type="checkbox" id="source_format_<?php echo esc_attr($format); ?>" name="source_format[]" value="<?php echo esc_attr($format); ?>" <?php echo esc_attr($checked); ?>>
							<label for="source_format_<?php echo esc_attr($format); ?>"><?php echo esc_html(strtoupper($format)); ?></label>
						</div>
					<?php } ?>
				</div>
			</div>
			<div class="swift-train-webp-converter-setting-field">
				<label><?php esc_html_e('Conversion Quality', SWIFT_TRAIN_WEBP_CONVERTER_SLUG); ?>:</label>
				<input type="range" id="conversion_quality" name="conversion_quality" min="1" max="100" step="1" value="<?php echo esc_attr($settings['quality']); ?>">
				<p>
					<?php printf(esc_html__('Conversion quality: %s%%', SWIFT_TRAIN_WEBP_CONVERTER_SLUG), '<span id="conversion_quality_value">' . esc_html($settings['quality']) . '</span>'); ?><br>
					<?php printf(__('Lower values yield better compression but lower image quality. Default (92) is a good balance between image quality and compression. Please set a value between 1 ~ 100', SWIFT_TRAIN_WEBP_CONVERTER_SLUG)) ?>
				</p>
			</div>
			<div class="swift-train-webp-converter-setting-field">
				<label><?php esc_html_e('Delete source file', SWIFT_TRAIN_WEBP_CONVERTER_SLUG); ?>:</label>
				<div class="swift-train-webp-converter-toggle">
					<input type="checkbox" id="delete_source_file" name="delete_source_file" <?= $settings['delete_source_file'] == 1 ? 'checked' : '' ?>>
					<label for="delete_source_file"><?php esc_html_e('Delete source file to free up some disk space.', SWIFT_TRAIN_WEBP_CONVERTER_SLUG) ?></label>
				</div>
			</div>
		</div>
	</div>

	<?php if (false): ?>
		<div class="section actions">
			<h2>Advanced Settings</h2>
			<div class="section-content">
				<!-- contents of advanced settings section -->
			</div>
		</div>
	<?php endif ?>

	<div class="section actions">
		<div class="swift-train-webp-converter-settings-save">
			<?php wp_nonce_field('swift_train_webp_converter_save_settings', 'swift_train_webp_converter_nonce'); ?>
			<button id="swift-train-webp-converter-save-btn" class="button button-primary" type="button">
				<?php esc_html_e('Save', SWIFT_TRAIN_WEBP_CONVERTER_SLUG); ?>
			</button>
		</div>
		<div class="ajax_message"></div>
	</div>

</div>
<script>
jQuery(function ($) {
	let timer,
		interval = 2000,
		$msg = $('#swift-train-webp-converter .ajax_message')

	// Handle the save button click
	$('#swift-train-webp-converter-save-btn').on('click', function () {
		let data, sourceFormat = []
		document.querySelectorAll('input[name="source_format[]"]:checked').forEach(function (el) {
			sourceFormat.push(el.value)
		})

		if (!sourceFormat.length) {
			$msg.text('<?php esc_html_e('At least one source format needs to be selected!', SWIFT_TRAIN_WEBP_CONVERTER_SLUG); ?>');
		}

		data = {
			action: 'swift_train_webp_converter_save_settings',
			conversion_quality: document.getElementById('conversion_quality').value,
			delete_source_file: document.getElementById('delete_source_file').checked ? 1 : 0
			source_format: sourceFormat,
		}
		console.log(data)

		$.ajax({
			type: 'post',
			url: ajaxurl,
			data: data,
			beforeSend() {
				clearTimeout(timer)
				$msg.text('<?php esc_html_e('Saving...', SWIFT_TRAIN_WEBP_CONVERTER_SLUG); ?>');
			},
			success(res) {
				if (res.success) {
					$msg.text('Saved');
					timer = setTimeout(function () {
						$msg.text('');
					}, interval)
				} else {
					$msg.text(res.data);
				}
			}
		})
	})

	document.getElementById('conversion_quality').addEventListener('input', function () {
		document.getElementById('conversion_quality_value').textContent = this.value;
	});
})
</script>
