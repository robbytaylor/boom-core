/**
@fileOverview jQuery plugins written specifically for Boom.
*/

/**
@namespace
@name $.fn
*/

(function($) {

	/**
	@function
	*/
	$.fn.ui = function(opts){
		$.boom.log('Start bind UI events');

		this.find('.boom-tabs').tabs();
		this.find('.boom-datepicker').datetimepicker($.boom.config.datepicker);

		$.boom.log('Stop bind UI events');

		return this;
	};

	$.fn.assetManagerImages = function() {
		$(this).each(function() {
			var $this = $(this),
				asset = new boomAsset($this.attr('data-asset')),
				url  = asset.getUrl('thumb', $this.width(), $this.height()) + '?' + Math.floor(Date.now() / 1000);

			$this.find('img').attr('src', url);	
		});
	};
})( jQuery );