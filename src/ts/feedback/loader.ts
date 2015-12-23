export class Loader {
	constructor() {
		var img = new Image();
		img.src = '/vendor/boomcms/boom-core/img/ajax_load.gif';

		this.$element = $('<div id="b-loader"></div>').appendTo($(top.document).find('body'));
		this.bind_loader_to_global_ajax_events();
	}

	private bind_loader_to_global_ajax_events() {
		var loader = this;

		$(this.document)
			.bind("ajaxSend", function(){
				loader.show();
			 })
			.bind("ajaxComplete", function(){
				loader.hide();
			 });
	}

	show() {
		this.$element.show();
	}

	hide() {
		this.$element.hide();
	}
});