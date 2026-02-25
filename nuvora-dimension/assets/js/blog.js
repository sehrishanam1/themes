(function($) {
	'use strict';

	function handlePageBtnClick(e) {
		var btn = e.target.closest('.nd-page-btn[data-page]');
		if ( ! btn ) return;

		// Stop the event completely so main.js close/hide logic doesn't fire
		e.stopImmediatePropagation();
		e.stopPropagation();

		var $btn   = $(btn);
		var $panel = $btn.closest('article.nuvora-blog-panel');
		var page   = parseInt( $btn.data('page'), 10 );

		if ( $panel.data('loading') ) return;
		$panel.data('loading', true);
		$btn.prop('disabled', true).text('Loading\u2026');

		$.ajax({
			url:    nuvoraBlog.ajaxurl,
			method: 'POST',
			data: {
				action: 'nuvora_load_page',
				page:   page,
			},
			success: function(res) {
				if ( res.success && res.data.html ) {
					var $list = $panel.find('#nuvora-blog-list');
					var $wrap = $panel.find('.nd-pagination-wrap');

					$list.add($wrap).animate({ opacity: 0 }, 180, function() {
						if ( $panel.data('swapped') ) return;
						$panel.data('swapped', true);

						var $incoming = $('<div>').append( $.parseHTML( res.data.html ) );
						var $newList  = $incoming.find('#nuvora-blog-list');
						var $newWrap  = $incoming.find('.nd-pagination-wrap');

						$panel.find('#nuvora-blog-list').replaceWith( $newList );

						var $currentWrap = $panel.find('.nd-pagination-wrap');
						if ( $newWrap.length ) {
							$currentWrap.replaceWith( $newWrap );
						} else {
							$currentWrap.remove();
						}

						if ( res.data.progress ) {
							$panel.find('.nd-progress-bar__fill').css('width', res.data.progress + '%');
						}

						$panel.find('#nuvora-blog-list').css('opacity', 0).animate({ opacity: 1 }, 250);
						$panel.find('.nd-pagination-wrap').css('opacity', 0).animate({ opacity: 1 }, 250);
						$panel.animate({ scrollTop: 0 }, 300);

						$panel.removeData('loading').removeData('swapped');
					});

				} else {
					$btn.prop('disabled', false).text('Error \u2014 try again');
					$panel.removeData('loading');
				}
			},
			error: function() {
				$btn.prop('disabled', false).text('Error \u2014 try again');
				$panel.removeData('loading');
			}
		});
	}

	// Use capture phase (true) so this fires BEFORE main.js stopPropagation can block it
	document.addEventListener('click', handlePageBtnClick, true);

})(jQuery);
