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
			'version'	=>	'1.5.6'
			);

	public $ft_name = "category_field";

	public $default_settings = array(
		'category_field_category_group_id' 	=> '',
		'category_field_display_type' 		=> '',
		'category_field_hide_filter' 		=> 'n',
		'category_field_hide_edit' 			=> 'n'
	);

	public $settings = array();


	// --------------------------------------------------------------------

	function install()
	{
		return $this->default_settings;
	}


	// --------------------------------------------------------------------

	function display_field($data)
	{

		$this->_field_includes();

		$this->EE->lang->loadfile('category_field');

		$group_id 		= $this->get_settings_prop('category_field_category_group_id');
		$display_type 	= $this->get_settings_prop('category_field_display_type');
		$hide_filter	= $this->get_settings_prop('category_field_hide_filter', 'n');
		$hide_edit		= $this->get_settings_prop('category_field_hide_edit', 'n');

		// If no group id select, exit and return message
		if($group_id == '') return lang('no_group_id');

		$this->EE->cp->add_to_foot('
			<script>
				$(document).ready(function() {
					$("#sub_hold_field_' . $this->field_id . '").categoryField({
						fieldId			: '.$this->field_id.',
						categoryGroupId	: ' . $group_id .',
						editText		: "' . lang('edit') .'",
						themesFolder	: "' . URL_THIRD_THEMES . '../cp_themes/",
						displayType		: ' . $display_type .',
						hideEdit		: "' . $hide_edit .'",
						fieldName		: "' . $this->field_name . '"
					});
				});
			</script>');

		$html = form_hidden($this->field_name);

		if($hide_filter != 'y'){
			$html .= '<input type="text" value="" id="cat_filter_group_' . $group_id . '" class="filter_input" placeholder="'. lang('filter_input_placeholder'). '"/>';
		}

		return $html;
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
						max-height:350px;
					}

					.publish_category_field .filter_input {
						width: 220px;
						background: #fffef2;
						margin-top: 4px;
					}

					.publish_category_field .cat_group_container label:hover {
						color: #000;
					}
					.publish_category_field .cat_group_container label:nth-child(2n){
						background-color: #f3f6f7; /*ie*/
						background-color: rgba(255,255,255,.4);
					}
					.category_field_select {
						margin-right: 8px;
					}
				</style>
				');

			$this->EE->cp->add_to_foot('<script>
				jQuery.extend (
					// contains case insensitive
				    jQuery.expr[":"].containsCI = function (a, i, m) {
				        var sText   = (a.textContent || a.innerText || "");
				        var zRegExp = new RegExp (m[3], "i");
				        return zRegExp.test (sText);
				    }
				);
				</script>');

			$this->cache[$this->ft_name]['included_configs'] = array();
		}
	}


	// --------------------------------------------------------------------

	function display_settings($data)
	{

		$this->EE->lang->loadfile('category_field');

		$settings = array_merge($this->default_settings, $data);


		$field_cat_groups = $this->get_field_cat_groups($data['group_id']);

		$category_group[''] = "None";

		$category_group_list[''] = "None";

		if(sizeof($field_cat_groups) > 0)
		{
			foreach($field_cat_groups as $category_group)
			{
				$category_group_list[$category_group['group_id']] = $category_group['group_name'];
			}

		}

		$category_field_display_type = array(
			0 => lang('display_checkbox'),
			1 => lang('display_select')
		);


		// Replace drop down with a notice if field's channel(s) are not properly set up
		$cat_group_html = (sizeof($field_cat_groups) > 0) ? form_dropdown('category_field_category_group_id', $category_group_list,  $settings['category_field_category_group_id']) : '<p class="notice">'.lang('no_cat_groups_assigned').'</p>';


		// Yes/No Options for Select Controls
		$select_options = array('y' => 'Yes', 'n' => 'No');

		$this->EE->table->add_row(
			'<strong>' . lang('category_group') .'</strong><br>' . lang('category_group_desc'), $cat_group_html
		);

		$this->EE->table->add_row(
			'<strong>' .lang('display_type').'</strong>',
			form_dropdown('category_field_display_type', $category_field_display_type, $settings['category_field_display_type'])
		);

		$this->EE->table->add_row(
			'<strong>' . lang('hide_filter') .'</strong><br>' . lang('hide_filter_desc'),
			 form_dropdown('category_field_hide_filter', $select_options, $settings['category_field_hide_filter'])
		);

		$this->EE->table->add_row(
			'<strong>' . lang('hide_edit') .'</strong><br>' . lang('hide_edit_desc'),
			form_dropdown('category_field_hide_edit', $select_options, $settings['category_field_hide_edit'])
		);

	}

	// ----------------------------------------------------------------

	private function get_field_cat_groups($field_id)
	{

		// bail now, field id is missing!
		if(!$field_id) return $rtn;

		// Get all channel_ids in case this fieldgroup is assigned to more than 1 channel

		$query = $this->EE->db->query("select channel_id from exp_channels where field_group = $field_id");
		if($query->num_rows() == 0) return array();

		// Convert results to comma delimited list so we can reuse in next query
		$ids=array();

		foreach($query->result_array() as $row)
		{
		    $ids[] = $row['channel_id'];
		}

		$channel_ids =  implode (',' , $ids );

		// Get Category Group IDs assigned to this field's parent channels
		$query = $this->EE->db->query("select cat_group from exp_channels where channel_id IN ($channel_ids) AND cat_group <> ''");
		if($query->num_rows() == 0) return array();

		// Since we may this fieldset assigned to multiple channels,
		// we need to combine all of those channels' categories into
		// a single list

		$ids_str = '';
		foreach($query->result_array() as $row)
		{
		    $ids_str .= $row['cat_group'];   
		}
		
		// convert to array split by pipes
		$ids =  preg_split('/[|]+/', $ids_str , -1, PREG_SPLIT_NO_EMPTY);
		
		// convert to comman del list for query
		$cat_group_ids =  implode (',' , $ids );
		
		// Check again after parsing, bail if its empty
		if($cat_group_ids == '') return array();

		// Finally get the category groups' data!
		$query = $this->EE->db->query("select group_id, group_name from exp_category_groups	where group_id IN($cat_group_ids)");
		if($query->num_rows() == 0) return array();


		return $query->result_array();
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

	// --------------------------------------------------------------------

	function validate ($data)
	{
		return;
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

	function pre_process($data)
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
