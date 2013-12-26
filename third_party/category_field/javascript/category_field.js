(function($) {

	$.categoryField = function(element, options) {

		var defaults = {
			themesFolder		: '/themes/',
			fieldId				: 0,			// Field ID,
			fieldName			: '',			// Field Name
			categoryGroupId		: 0,			// Category Group ID
			editLinkText		: 'Edit', 		// Edit categories replacement text
			minItemsToFilter	: 10,	 		// minimum items before displaying list
			hideEdit			: '',	 		// overrides hiding of the edit link
			displayType			: 0				// 0 = checkbox, 1 = dropdown (single choice)
		}

		var plugin = this;

		var $catgroup, $holder, $edit_link, $holder, $filter_input;

		plugin.settings = {}

		var $element = $(element),
			 element = element;


		// ----------------------------------------------------------------

		plugin.init = function() {
			
			// merge settings
			plugin.settings = $.extend({}, defaults, options);

			// Our input Element
			var $element = $(element);

			// Holder fieldset element
			$holder = $('#sub_hold_field_' + plugin.settings.fieldId  + ' fieldset');

			// Categories container element
			$catgroup = $('#cat_group_container_' + plugin.settings.categoryGroupId);

			// grab the edit link associated with this category group...
			$edit_link = $("#sub_hold_field_category a.edit_categories_link").filter( function (){
				return $(this).attr("href").match('(group_id=' + plugin.settings.categoryGroupId +')$');
			});
			
			if(plugin.settings.hideEdit != 'y')
			{
				// add an icon :D
				$edit_link.text(plugin.settings.editLinkText).prepend('<img src="' + plugin.settings.themesFolder +'default/images/icon-edit.png" alt=""/>&nbsp;');
				
				$edit_link.appendTo($holder);
			}else {
				$edit_link.remove();
			}
			
			// This fieldset will be empty in the categories tab after we move the $categroup in the DOM
			var $empty_fieldset = $catgroup.parent("fieldset");

			// Move the group to the $holder element
			$catgroup.prependTo($holder);


			// Grab the input text field added by Category Field fieldtype to use as a filtering control
			$filter_input = $('#cat_filter_group_' + plugin.settings.categoryGroupId);

			// make this a select list instead
			if(plugin.settings.displayType == 1)
			{
				createSelectList();
			}else
			{
				createChecboxList();
			}
			
			// Move the filter to the top 
			$holder.find('.filter_input').prependTo($holder);
			
			// Remove the empty fieldset now
			$empty_fieldset.remove();
		}

		var createSelectList = function ()
		{
			$filter_input.remove(); // we don't need a filter here

			if($catgroup.find('.category_field_select').length == 0)
			{
				$catgroup.append('<select name="category[]" class="category_field_select"/>');
			}

			var $selectList = $catgroup.find('select.category_field_select');
			
			$selectList.append('<option value="">Select</option><option disabled>--------------</option>');

			var	$label, $input, selected, label_text;

			$catgroup.find('label').each(function (){
				$label= $(this);
				$input = $label.find('input');
				selected = ($input.attr('checked') == 'checked') ?  'selected="selected"'  : '';
				// Strip beginning line breaks and spaces to allow for keyboard input -- does not work with indented items
				label_text = $label.text().replace(/(\r\n|\n|\r|\t)/gm, '').replace(/(^\s{2})/,'');
				$selectList.append('<option value="' + $input.val() + '" ' + selected +'>' + label_text + '</option>');
			});


			$edit_link.click(function (){
				$selectList.hide();
			});

			$holder.find('a.cats_done').live('click',function (){

				// wait a few ms to create drop down
				setTimeout(createSelectList,500);

				// if the drop down is above the scroll view, scroll up!
				scrollToView();
			});

			// remove label/inputs, we've got a select list now
			$catgroup.find('label').remove();

			// Add a value to field for validation by checking value of select
			$('form').bind('submit', function(e) {
				var v = $selectList.val();
				$('input[name="'+plugin.settings.fieldName+'"]').val(v);
			});

		}

		var removeSelectList = function ()
		{
			$catgroup.find('select.category_field_select').remove();
		}


		var createChecboxList = function ()
		{
			// Hide filter if we don't have enough items in the category group
			if($catgroup.find('label').length < plugin.settings.minItemsToFilter)
			{
				$filter_input.hide();
			}
			else{

				// Hide/show input filter when the edit/action links are clicked
				$edit_link.click(function (){
					$filter_input.hide();
				});
				$holder.find('a.cats_done').live('click',function (){
					$filter_input.val('').show();
					scrollToView();
				})

				// Bind keyboard events to input control
				$filter_input.keyup(function (){
					var val = $filter_input.val();
					$catgroup.find("label").css("display","");

					if(val == "")
					{
						return;
					}

					$catgroup.find("label").not(":containsCI("+val+")").css("display","none");
				}).val('');
			}


			// Add a value to field for validation by counting the total number of checked categories
			$('form').bind('submit', function(e) {
				var v = $catgroup.find(':checked').length;
				// make 0 an empty string since the system treats 0 as a valid entry
				if(v == 0)  v = '';
				$('input[name="'+plugin.settings.fieldName+'"]').val(v);
			});

		}

		var scrollToView = function ()
		{
			// if the drop down is above the scroll view, scroll up!
			if($catgroup.offset().top < $(window).scrollTop())
			{
				$('html, body').animate({scrollTop: $holder.offset().top-100}, 500);
		    }
		}

		// ----------------------------------------------------------------

		plugin.init();

	}

	$.fn.categoryField = function(options) {

		return this.each(function() {
			if (undefined == $(this).data('categoryField')) {
				var plugin = new $.categoryField(this, options);
				$(this).data('categoryField', plugin);
			}
		});

	}

})(jQuery);