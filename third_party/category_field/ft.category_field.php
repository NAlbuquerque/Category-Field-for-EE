<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * ExpressionEngine Category_field Class
 *
 * @package		Category Field
 * @category	Fieldtypes
 * @author		Nuno Albuquerque
 * @license		http://creativecommons.org/licenses/by/3.0/
 * @link		http://nainteractive.com
 */

class  Category_field_ft extends EE_Fieldtype {
	
	var $info = array(
			'name'		=>	'Category Field',
			'version'	=>	'1.1'
			);
	

	// --------------------------------------------------------------------

	function install()
	{
		return array(
			'category_group_id'	=> ''
		);
	}

	
	// --------------------------------------------------------------------

	function display_field()
	{
		$this->_field_includes();
		$this->EE->lang->loadfile('category_field');
		$category_group_id = $this->settings['category_group_id']; 

		$this->EE->cp->add_to_head('
			<script>
				$(document).ready(function() {

					$("#sub_hold_field_' . $this->field_id . '").categoryField({
						fieldId:'.$this->field_id.',
						categoryGroupId: ' . $category_group_id .',
						editText	: "' . lang('edit') .'",
						themesFolder:	"' . URL_THIRD_THEMES . '../cp_themes/"
					});
					
				});
			</script>');
		
		return '<input type="text" value="" id="cat_filter_group_' . $category_group_id . '" class="filter_input" placeholder="Filter list..."/>';
	}
	
	
	// ---------------------------------------------------------------- 
	
	function _field_includes()
	{
		if (! isset($this->cache['included_configs']))
		{		
/* 			$this->EE->load->library('javascript'); */
			$this->EE->load->library('javascript');
			$this->EE->cp->load_package_js('category_field');
			
			$this->EE->cp->add_to_head('
				<style>
					.publish_category_field div.cat_group_container {
						margin: 8px 0;
						padding: 10px;
						border: 1px solid #D0D7DF;
						overflow:auto;
					}
					
					.publish_category_field .filter_input {
						width: 260px;
					}
				</style>
				
				
				<script>
				jQuery.extend (
				    jQuery.expr[":"].containsCI = function (a, i, m) {
				        var sText   = (a.textContent || a.innerText || "");     
				        var zRegExp = new RegExp (m[3], "i");
				        return zRegExp.test (sText);
				    }
				);

				</script>
				');
			
			$this->cache['included_configs'] = array();
		}
	}
	

	// --------------------------------------------------------------------
	
	function display_settings($data)
	{
		// Display as List, Tree, or Drop Down
		// Optionally add "other", store in field 
		
		$this->settings = array_merge($this->settings, $data);
		$this->EE->db->select('group_id, group_name');
		$this->EE->db->from('exp_category_groups');
		$query = $this->EE->db->get();
		
		$category_group[''] = "None";
		$category_group_list[''] = "None";
		
		foreach($query->result_array() as $category_group)
		{
			$category_group_list[$category_group['group_id']] = $category_group['group_name']; 
		}
		
		$this->EE->table->add_row(
			'Category Group', form_dropdown('category_group_id',$category_group_list, $this->settings['category_group_id'])
		);
	}
	
	
	// ---------------------------------------------------------------- 
	
	function save_global_settings()
	{
		return array_merge($this->settings, $_POST);
	}
	
	// ---------------------------------------------------------------- 
	
	function save($field_data = '')
	{
		// We don't need to store any information, our category data is stored by EE automatically
		return 'none';
	}
	
	
	// --------------------------------------------------------------------

	function save_settings ($data)
	{
		$settings['field_fmt'] = 'none';
		$settings['field_show_fmt'] = 'n';
		return array_merge($this->settings, $_POST);
	}
	
	
	/**
	 * Displays the field data in a template tag.
	 *
	 * @access	public
	 * @param	array 		$params				The template tag parameters (key / value pairs).
	 * @param	string		$tagdata			The content between the opening and closing tags, if it's a tag pair.
	 * @param 	string		$field_data			The field data.
	 * @param 	array 		$field_settings		The field settings.
	 * @return	string
	 */
	function replace_tag($data, $params = array(), $tagdata = FALSE)
	{
		// TODO
		// return selected categories in this group for current entry
		
		return $this->settings['category_group_id'];
	}
	
} 
// END  Category_field_ft class

/* End of file ft.Category_field.php */
/* Location: ./system/expressionengine/third_party/Category_field/*/
