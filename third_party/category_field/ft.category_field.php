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

	public $info = array(
			'name'		=>	'Category Field',
			'version'	=>	'1.4'
			);
			
	public $ft_name = "category_field";
	
	public $default_settings = array(
		'category_field_category_group_id' 	=> '', 
		'category_field_display_type' 		=> '', 
		'category_field_show_filter' 		=> ''
	);
	
	public $settings = array();
	

	// --------------------------------------------------------------------

	function install()
	{
		return $this->default_settings;
	}


	// --------------------------------------------------------------------

	function display_field()
	{
		$this->_field_includes();
		
		$this->EE->lang->loadfile('category_field');

		$group_id 		= $this->get_settings_prop('category_field_category_group_id');
		$display_type 	= $this->get_settings_prop('category_field_display_type');
		$show_filter	= $this->get_settings_prop('category_field_show_filter', 'y');

		$this->EE->cp->add_to_head('
			<script>
				$(document).ready(function() {
					$("#sub_hold_field_' . $this->field_id . '").categoryField({
						fieldId			: '.$this->field_id.',
						categoryGroupId	: ' . $group_id .',
						editText		: "' . lang('edit') .'",
						themesFolder	: "' . URL_THIRD_THEMES . '../cp_themes/",
						displayType		: ' . $display_type . ',
					});
				});
			</script>');

		$html = '<input type="text" value="" id="cat_filter_group_' . $group_id . '" class="filter_input" placeholder="'. lang('filter_input_placeholder'). '"/>';

		return $show_filter ? $html : '';
	}


	// ----------------------------------------------------------------

	function _field_includes()
	{
		if (! isset($this->cache[$this->ft_name]['included_configs']))
		{
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
						width: 180px;
						background: #fffef2;
						margin-top: 4px;
					}

					.category_field_select {
						margin-right: 8px;
					}
				</style>


				<script>
				jQuery.extend (
					// contains case insensitive
				    jQuery.expr[":"].containsCI = function (a, i, m) {
				        var sText   = (a.textContent || a.innerText || "");
				        var zRegExp = new RegExp (m[3], "i");
				        return zRegExp.test (sText);
				    }
				);

				</script>
				');

			$this->cache[$this->ft_name]['included_configs'] = array();
		}
	}


	// --------------------------------------------------------------------

	function display_settings($data)
	{
		$this->EE->lang->loadfile('category_field');
		
		$settings = array_merge($this->default_settings, $data);
		
		$this->EE->db->select('group_id, group_name');
		
		$this->EE->db->from('exp_category_groups');
		
		$query = $this->EE->db->get();

		$category_group[''] = "None";
		
		$category_group_list[''] = "None";

		foreach($query->result_array() as $category_group)
		{
			$category_group_list[$category_group['group_id']] = $category_group['group_name'];
		}

		$category_field_display_type = array(
			0 => lang('display_checkbox'),
			1 => lang('display_select')
		);

		$this->EE->table->add_row(
			lang('category_group'), form_dropdown('category_field_category_group_id',$category_group_list,  $settings['category_field_category_group_id'])
		);

		$this->EE->table->add_row(
			lang('display_type'), form_dropdown('category_field_display_type',$category_field_display_type, $settings['category_field_display_type'])
		);

		$this->EE->table->add_row(
			lang('show_filter'), form_checkbox('category_field_show_filter','y', $settings['category_field_show_filter'])
		);
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
		$settings = array();

		foreach ($this->default_settings AS $setting => $value)
		{
			if (($settings[$setting] = $this->EE->input->post($setting)) === FALSE)
			{
				$settings[$setting] = $value;
			}
		}
		
		return $settings;
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
		return $this->settings['category_field_category_group_id'];
	}

	// --------------------------------------------------------------------

	protected function get_settings_prop($key, $default = '')
	{
		if(array_key_exists($key, $this->settings))
		{
			return $this->settings[$key];
		}
		return $default;
	}

}
// END  Category_field_ft class

/* End of file ft.Category_field.php */
/* Location: ./system/expressionengine/third_party/Category_field/*/
