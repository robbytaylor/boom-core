/**
Common functionality for all editable slots
@class
@name $.ui.chunk
*/
$.widget('boom.boomMenu', {
	_create : function() {
		this._build_menu();
		this._bind_profile();
	},

	_bind_profile : function() {
		this.element
			.find('a[href="/cms/profile"]')
			.on('click', function(e) {
				e.preventDefault();

				$.boom.dialog.open({
					'url': '/cms/profile',
					'title': 'User profile',
					callback: function() {
						$.post(url, $('#b-people-profile').serialize())
							.done(function() {
								$.boom.growl.show('Profile updated');
							});
					}
				});
			});
	},

	_build_menu : function() {
		this.element
			.find('span')
			.splitbutton({
				items: this._find_items(),
				width: 'auto',
				menuPosition: 'right',
				split: false
			});
	},

	_find_items : function() {
		var menu_items = {};

		this.element
			.find('ul')
			.children()
			.find('a')
			.each(function() {
				var $this = $(this), item = [];

				item[$this.text()] = function() {
					$this[0].click();
				};
				menu_items = $.extend(menu_items, item);
			});

		return menu_items;
	}
});