$.widget('boom.pageTitle', $.ui.chunk, {
	lengthCounterCreated : false,

	/* The length at which the title length inidcator turns red */
	softLimit: 70,

	/* The length at which the title won't save */
	hardLimit: 100,

	saveOnBlur: false,

	bind : function() {
		$.ui.chunk.prototype.bind.call(this);

		var self = this,
			element = this.element,
			old_text = this.getTitle();

		this.element.textEditor({
			edit : function() {
				var title = self.getTitle();

				if (title != '' && title != old_text && title.length <= self.hardLimit) {
					self.updatePageTitle(old_text, title);
					self._save(title, old_text);
				}

				old_text = title;
				self.removeTitleLengthCounter();
			}
		});

		this.element
			.on('keydown change paste', function() {
				var oldText = self.getTitle();

				setTimeout(function() {
					self.updatePageTitle(oldText, self.getTitle());
					self._update_length_counter(self.getLength());
				}, 0);
			})
			.on('focus', function() {
				if (self.isUntitled()) {
					self.element.text('');
				}

				if ( ! self.lengthCounterCreated) {
					self._create_length_counter(self.getLength());
					self.lengthCounterCreated = true;
				}
			});
	},

	_create_length_counter : function() {
		var $counter = $('<div id="b-title-length"><span></span></div>');

		$(top.document)
				.find('body')
				.first()
				.append($counter);

		var offset = this.element.offset(),
			title = this;

		$counter
			.css({
				top : offset.top + 'px',
				left : (offset.left - 110) + 'px'
			});

		$('<p><a href="#" id="b-title-help">What is this?</a></p>')
			.appendTo($counter)
			.on('mousedown', 'a', function() {
				title.element.textEditor('disableAutoSave');
			})
			.on('keydown', function(e) {
				if (e.which == 13) {
					title.openHelp();
				}
			})
			.on('click', function(e) {
				e.preventDefault();

				title.openHelp();
			});

		this._update_length_counter(this.getLength());
	},

	edit : function() {},

	_get_counter_color_for_length : function(length) {
		if (length >= this.softLimit) {
			return 'red';
		} else if (length >= this.softLimit * 0.9) {
			return 'orange';
		} else if (length >= this.softLimit * 0.8) {
			return 'yellow';
		}

		return 'green';
	},

	getLength: function() {
		return this.getTitle().length;
	},

	getTitle: function() {
		return this.element.text().trim();
	},

	isUntitled : function() {
		return this.getTitle() === 'Untitled';
	},

	openHelp : function() {
		var title = this;

		new boomDialog({
			url : '/vendor/boomcms/boom-core/html/help/title_length.html',
			width : '600px',
			cancelButton: false
		}).always(function() {
			title.element.textEditor('enableAutoSave');
			title.element.focus();
		});
	},

	removeTitleLengthCounter: function() {
		this.lengthCounterCreated = false;
		$(top.document).find('#b-title-length').remove();
	},

	_save : function(title, old_title) {
		this.options.currentPage.setTitle(title)
			.done(function(data) {
				if (data.location !== top.window.location) {
					var history = new boomHistory();

					if (history.isSupported()) {
						history.replaceState({}, title, data.location);
						new boomNotification('Page title saved.');
						$.boom.page.toolbar.status.set(data.status);
					} else {
						var confirmation = new boomConfirmation('Page URL changed', "Because you've set a page title for the first time the URL of this page has been updated to reflect the new title.<br /><br />Would you like to reload the page using the new URL?<br /><br />You can continue editing the page without reloading.");
						confirmation
							.done(function() {
								top.location = data.location;
							});
					}
				} else {
					new boomNotification('Page title saved.');
					$.boom.page.toolbar.status.set(data);
				}

				var page_title = top.$('title').text().replace(old_title, title);
				top.$('title').text(page_title);
			});
	},

	updatePageTitle : function(oldTitle, newTitle) {
		top.document.title = top.document.title.replace(oldTitle, newTitle);
	},

	_update_length_counter : function(length) {
		$(top.document).find('#b-title-length')
			.find('span')
			.text(length)
			.end()
			.css('background-color', this._get_counter_color_for_length(length));

		var disable_accept_button = (length >= this.hardLimit || length === 0)? true : false;
		var opacity = disable_accept_button? '.35' : 1;
		$('.b-editor-accept')
			.prop('disabled', disable_accept_button)
			.css('opacity', opacity);
	},

	unbind : function() {}
});