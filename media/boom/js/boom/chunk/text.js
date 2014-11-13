/**
Editable text slots
@class
@name chunkText
@extends $.ui.chunk
@memberOf $.ui
*/
$.widget('ui.chunkText', $.ui.chunk,

	/**
	@lends $.ui.chunkText
	*/
	{

	content : '',

	_create : function() {
		var element = this.element.find('.chunk-text');
		this.element = (element.length)? $(element[0]) : this.element;

		$.ui.chunk.prototype._create.call(this);
	},

	bind : function() {
		var element = this.element;

		$.ui.chunk.prototype.bind.call(this);

		this.element
			.on('click', function() {
				element.focus();
			})
			.on('blur', function() {
				$('body').editor('blur', element);
			});
	},

	/**
	Make the element editable by invokeing boom.editor.edit() on it.
	*/
	edit : function(){

		var self = this;

		$.boom.log('Text chunk slot edit');

		this.element[0].id = this.element[0].id || $.boom.util.dom.uniqueId('boom-dom-wysiwyg-');

		var old_html = self.element.html();

		if (this.element.text() == 'Default text.') {
			this.element.html( '' );
		}

		this.element.unbind('keydown');

		$('body').editor('edit', this.element)
			.fail(function() {
				self.element.html( old_html ).show();
				self.destroy();
			})
			.done(function() {
				var edited = old_html != self.element.html();

				if ( ! self.hasContent()) {
					self.remove();
				} else if (edited == true) {
					self._save();
				} else {
					self.bind();
				}
			});
	},

	/**
	Get the chunk HTML, escaped and cleaned.
	*/
	getData : function(){
		var $content = this.element.find('.slot-content');

		this.content = ($content.length)? $content.html() : this.element.html();

		return {
			text : this.content,
			is_block : this.isBlockLevel()? 1 : 0
		};
	},

	hasContent : function() {
		return this.element.text() !== '' || this.element.find('img').length > 0;
	},

	isBlockLevel : function() {
		return this.element.is('div');
	},

	_update_html : function() {
		this.bind();
	},

	unbind : function() {
		this.element.unbind('click');
	}
});