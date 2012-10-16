(function($) {

	$.categoryField = function(element, options) {

		var defaults = {
			themesFolder		: '/themes/',
			fieldId				: 0,			// Field ID,
			categoryGroupId		: 0,			// Category Group ID
			editLinkText		: 'Edit', 		// Edit categories replacement text
			minItemsToFilter	:	10, 		// minimum items before displaying list
		}

		var plugin = this;

		plugin.settings = {}

		var $element = $(element),
			 element = element;
		
		
		// ---------------------------------------------------------------- 
		
		plugin.init = function() {
			plugin.settings = $.extend({}, defaults, options);
			// code goes here

			// Our input Element
			var $element = $(element);
			
			// Holder fieldset element
			var $holder = $('#sub_hold_field_' + plugin.settings.fieldId  + ' fieldset');
			
			// Categories container element
			var $catgroup = $('#cat_group_container_' + plugin.settings.categoryGroupId);
			
			// This fieldset will be empty in the categories tab after we move the $categroup in the DOM
			var $empty_fieldset = $catgroup.parent("fieldset");
			
			// Move the group to the $holder element
			$catgroup.appendTo($holder);
			
			// Remove the empty fieldset now
			$empty_fieldset.remove();
			
			// grab the edit link associated with this category group...
			$edit_link = $("a.edit_categories_link").filter(
				function (){
					return $(this).attr("href").match('group_id=' + plugin.settings.categoryGroupId);
				});
				
			//...and append it to our holder
			$edit_link.appendTo($holder).end().text(plugin.settings.editLinkText).prepend('<img src="' + plugin.settings.themesFolder +'default/images/icon-edit.png" alt=""/>&nbsp;');
						
			// Grab the input text field added by Category Field fieldtype to use as a filtering control
			var $filter_input = $('#cat_filter_group_' + plugin.settings.categoryGroupId);

			// Hide if we don't have enough items in the category group
			if($catgroup.find('label').length < plugin.settings.minItemsToFilter)
			{
				$filter_input.hide();
			}
			else{
				
				// Hide/show input filter when the edit/action links are clicked
				$edit_link.click(function (){$filter_input.hide()});
				$holder.find('a.cats_done').live('click',function (){$filter_input.show();})
				
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