(function ($) {
	'use strict';

	$(function () {
		if ($.fn.wpColorPicker) {
			$('.hdinv-color-picker').wpColorPicker();
		}

		var frame;
		var $select = $('#hdinv-logo-select');
		var $remove = $('#hdinv-logo-remove');
		var $preview = $('#hdinv-logo-preview');
		var $idField = $('#hdinv-logo-id');

		if (!$select.length) {
			return;
		}

		$select.on('click', function (event) {
			event.preventDefault();

			if (frame) {
				frame.open();
				return;
			}

			frame = wp.media({
				title: $select.data('title') || 'Select Logo',
				multiple: false,
				library: { type: 'image' }
			});

			frame.on('select', function () {
				var attachment = frame.state().get('selection').first().toJSON();
				var url = (attachment.sizes && attachment.sizes.medium) ? attachment.sizes.medium.url : attachment.url;

				$idField.val(attachment.id);
				$preview.attr('src', url).show();
				$remove.show();
			});

			frame.open();
		});

		$remove.on('click', function (event) {
			event.preventDefault();
			$idField.val('');
			$preview.hide().attr('src', '');
			$remove.hide();
		});
	});
})(jQuery);
