$.widget('boom.assetManager', {
	baseUrl : '/cms/assets/',
	filters : {},
	selected : [],

	tag : 0,
	sortby : '',

	bind : function() {
		this.bindContentArea();
		this.bindMenuButtons();
	},

	bindContentArea : function() {
		var assetManager = this;

		this.element
			.delegate('#b-assets-pagination a', 'click', function(e) {
				e.preventDefault();

				$.get('/cms/assets/list?' + $(this).attr('href').split('?')[ 1 ])
					.done(function(data) {
						assetManager.showContent(data);
					});

				return false;
			})
			.on('change', '#b-assets-sortby', function(event) {
				assetManager.sortBy(this.value);
			})
			.on('change', '#b-assets-types', function(event) {
				if (this.selectedIndex) {
					assetManager.filterByType(this.options[this.selectedIndex].innerHTML);
				} else {
					assetManager.filterByType();
				}

			})
			.on('click', '#b-assets-all', function(event) {
				//assetManager.removeFilters();
				$.boom.history.load('');
				assetManager.listAssets();
			})
			.on('click', '.thumb a', function(event) {
				event.preventDefault();

				var $this = $(this);

				assetManager.select($this.attr('href').replace('#asset/', ''));
				$this.parent().parent().toggleClass('selected');
			});

		this.titleFilter = this.element.find('#b-assets-filter-title')
			.autocomplete({
				delay: 200, // Time to wait after keypress before making the AJAX call.
				minLength : 0,
				source: function(request, response){
					$.ajax({
						url: '/cms/autocomplete/assets',
						dataType: 'json',
						data: {
							text : assetManager.titleFilter.val()
						}
					})
					.done(function(data) {
						response(data);

						assetManager.filterByTitle(assetManager.titleFilter.val());
					});
				},
				select: function(event, ui){
					assetManager.filterByTitle(ui.item.value);
					$(".ui-menu-item").hide();
				}
			});
	},

	bindMenuButtons : function() {
		var assetManager = this;

		this.menu
			.on('click', '#b-button-multiaction-delete', function() {
				var asset = new boomAsset(assetManager.selected.join('-'));

				asset.delete()
					.done(function() {
						assetManager.listAssets();
						assetManager.clearSelection();
				});
			})
			.on('click', '#b-button-multiaction-edit', function() {
				var asset = new boomAsset(assetManager.selected.join('-'));

				$.boom.history.load('asset/' + assetManager.selected.join('-'))
					.done(function(response) {
						assetManager.showContent(response);

						$('#b-assets-content').asset({
							asset_id : asset.id
						});
					});

				assetManager.clearSelection();
			})
			.on('click', '#b-button-multiaction-download', function() {
				var asset = new boomAsset(assetManager.selected.join('-'));

				asset.download();
			})
			.on('click', '#b-button-multiaction-clear', function() {
				assetManager.clearSelection();
			})
			.on('click', '#b-button-multiaction-tag', function() {
				var asset = new boomAsset(assetManager.selected.join('-')),
					dialog;

				dialog = new boomDialog({
					url: '/cms/tags/asset/list/' + asset.id,
					title: 'Asset tags',
					width: 440,
					cancelButton : false,
					onLoad: function() {
						$('#b-tags').tagger({
							type: 'asset',
							id: asset.id
						});
					}
				});
			});
	},

	buildUrl : function(){
		var params = 'tag=' + this.tag_id + '&' +'sortby=' + this.sortby;

		for (var filter in this.filters) {
			if (this.filters[filter]) {
				params += '&' + filter + '=' + this.filters[filter];
			}
		}

		return this.baseUrl + 'list' + '?' + params;
	},

	clearSelection : function() {
		this.selected = [];
		this.toggleButtons();

		this.element.find('#b-assets-view-thumbs div').removeClass('selected');
	},

	_create : function() {
		this.menu = this.element.find('#b-topbar');
		this.bind();
		this.route();

		this.filters = this.options.filters? this.options.filters : this.filters;

		this.listAssets();
	},

	filterByType : function(type) {
		this.filters.type = type;
		this.listAssets();
	},

	filterByTitle : function(title) {
		this.filters.title = title;
		this.listAssets();
	},

	listAssets : function() {
		var assetManager = this;

		$.get(this.buildUrl())
			.done(function(response) {
				assetManager.showContent(response);
			});
	},

	removeFilters : function() {
		this.element.find('#b-assets-types').val(0);

		var $title = this.element.find('#b-assets-filter-title');
		$title.val($title.attr('placeholder'));

		this.removeTagFilters();
		this.listAssets();
	},

	removeTagFilters : function() {
		this.tag.filters = {};

		$('#b-tags-search')
			.find('.b-filter-input')
			.each(function() {
				var $this = $(this);
				$this.val($this.attr('placeholder'));
			})
			.end()
			.find('.b-tags-list li')
			.remove();
	},

	route : function() {
		var self = this;

		$.boom.history.route(
			function(segments){
				segments = segments.split('/');

				var
					item = segments[0],
					id = segments[1],
					asset = new boomAsset(id);

				if (item == 'asset' && id) {
					return asset
						.get()
						.done(function(response) {
							self.showContent(response);
						});
				}
			},
			function() {
				self.listAssets();
			}
		);
	},

	select : function(asset_id) {
		var index = this.selected.indexOf(asset_id);

		if (index == -1) {
			this.selected.push(asset_id);
		} else {
			this.selected.splice(index, 1);
		}

		this.toggleButtons();
	},

	showContent : function(content) {
		this.selected = [];
		this.toggleButtons();
		var $content = $(content);

		var id = $($content.get(0)).attr('id');
		var pagination = $content.get(2);
		var stats = $content.get(4);

		if (id == 'b-assets-content') {
			$('#b-assets-content')
				.replaceWith($content.get(0))
				.ui();
		} else {
			$('#b-assets-content')
				.html($content.get(0))
				.ui();
		}
		$('#b-assets-view-thumbs').justifyAssets();


		if (pagination) {
			$('#b-assets-pagination').replaceWith(pagination);
			$('#b-assets-filters').show();
			$('#b-assets-buttons').show();
		} else {
			$('#b-assets-pagination').contents().remove();
			$('#b-assets-filters').hide();
			$('#b-assets-buttons').hide();
		}

		if (stats) {
			$('#b-assets-stats').replaceWith(stats);
		} else {
			$('#b-assets-stats').contents().remove();
		}
	},

	sortBy : function(sort) {
		this.sortby = sort;
		this.listAssets();
	},

	toggleButtons : function() {
		var buttons = $('[id|=b-button-multiaction]').not('#b-button-multiaction-edit');
		$('#b-button-multiaction-edit').button(this.selected.length == 1 ? 'enable' : 'disable');
		buttons.button(this.selected.length > 0 ? 'enable' : 'disable');
	}
});