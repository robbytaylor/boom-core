/**
* Common functionality for the embedded CMS toolbar
* @class
* @name self.boom.page.toolbar
*/
$.widget( 'boom.pageToolbar', {
	buttons : {},
	closeSettingsOnPublish: false,

	_bindButtonEvents : function() {
		var self = this;

		this.element.contents()
			.on('click', '#b-page-delete', function() {
				self.options.page.delete()
					.done(function(response) {
						new boomNotification("Page deleted, redirecting to parent.");
						top.location = response;
					});
			})
			.on('click', '#b-page-addpage', function() {
				self.options.page.add()
					.done(function(response) {
						top.location = response.url;
					})
					.fail(function(response) {
						new boomAlert(response);
					});
			})
			.on('click', '#boom-page-editlive', function() {
				self.options.page.stash();
			})
			.on('click', '.b-page-visibility', function() {
				self.$settings.pageSettings('show', 'visibility');
				self.openPageSettings();
			})
			.on('click', '.b-button-preview', function() {
				$.boom.editor.state($(this).attr('data-preview'));
			})
			.on('click', '#b-page-template', function() {
				self.$settings.pageSettings('show', 'template');
				self.openPageSettings();
			})
			.on('click', '#b-menu-button', function() {
				var $body = $('body');

				if ($body.hasClass('pushy-active')) {
					$(top.window).trigger('boom:dialog:open');
				} else {
					$(top.window).trigger('boom:dialog:close');
				}
			})
			.on('click', '#b-page-settings', function() {
					self.openPageSettings();
			})
			.on('click', '#b-page-version-status', function() {
				self.$settings.pageSettings('show', 'drafts');
				self.openPageSettings();
				self.closeSettingsOnPublish = true;
			});

		this.buttonBar = this.element.contents().find('#b-topbar');
	},
	
	closePageSettings: function() {
		var toolbar = this;

		this.element
			.contents()
			.find('#b-page-settings-toolbar')
			.removeClass('open');

		setTimeout(function() {
			toolbar.minimise();
			$(top.window).trigger('boom:dialog:close');
		}, 1000);
	},

	_create : function() {
		var toolbar = this;

		$.boom.log('init CMS toolbar');

		this.findButtons();

		this._toggle_view_live_button();
		this.status = $('#b-page-version-status')
			.pageStatus({
				page : this.options.page,
				publishable : this.options.publishable
			})
			.data('boom-pageStatus');

		this.$settings = this.element
			.contents()
			.find('.b-page-settings')
			.pageSettings({
				page: toolbar.options.page,
				close: function() {
					toolbar.closePageSettings();
				},
				draftsSave: function(event, data) {
					if (data.action === 'revert') {
						$.boom.reload();
					} else {
						toolbar.status.set(data.status);
						
						if (data.status === 'published' && toolbar.closeSettingsOnPublish) {
							toolbar.closePageSettings();
						}
					}
				},
				templateSave: function() {
					toolbar.status.set('draft');

					new boomConfirmation('Reload page?', "Do you want to reload the page to view the new template?")
						.done(function() {
							$.boom.reload();
						});
				},
				visibilitySave: function(event, response) {
					if (response == 1) {
						toolbar.buttons.visible.show();
						toolbar.buttons.invisible.hide();
					} else {
						toolbar.buttons.visible.hide();
						toolbar.buttons.invisible.show();
					}

					toolbar._toggle_view_live_button();
				},
				urlsSave: function(event, primaryUrl) {
					var history = new boomHistory();
					history.replaceState({},
						top.window.document.title,
						'/' + primaryUrl
					);
				}
			});

		this._bindButtonEvents();
	},

	findButtons : function() {
		this.buttons = {
			visible : this.element.contents().find('#b-page-visible'),
			invisible : this.element.contents().find('#b-page-invisible'),
			viewLive : this.element.contents().find('#b-page-viewlive')
		};
	},

	/**
	* extend the toolbar to cover the entire window
	* @function
	*/
	maximise : function() {
		$.boom.log('maximise iframe');

		this.element.css({
			width : '100%',
			'z-index' : 100002
		});
	},

	/**
	* minimise the toolbar to allow clicking on the underlying page
	* @function
	*/
	minimise : function() {
		$.boom.log('minimise iframe');

		this.element.css({
			width : '60px',
			'z-index' : 10000
		});
	},

	openPageSettings: function() {
		this.closeSettingsOnPublish = false;
		this.maximise();

		this.element
			.contents()
			.find('#b-page-settings-toolbar')
			.addClass('open');

		$(top.window).trigger('boom:dialog:open');
	},

	/**
	@function
	*/
	hide : function() {
		this.buttonBar.css('z-index', 1);
	},

	/**
	@function
	*/
	show : function() {
		this.buttonBar.css('z-index', 10000);
	},

	_toggle_view_live_button : function() {
		if (this.buttons.visible.css('display') == 'none') {
			this.buttons.viewLive
				.attr('title', 'You cannot view a live version of this page as it is currently hidden from the live site')
				.prop('disabled', true);
		} else {
			this.buttons.viewLive
				.attr('title', 'View the page as it appears on the live site')
				.prop('disabled', false);
		}
	}
});
