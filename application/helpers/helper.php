<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * -----------------------------------
 * Custom helper file based on bootstrap
 * 
 * @author Dijo David
 * -----------------------------------
 */

if ( ! function_exists('get_files'))
{
	/**
	 * Get the array of files in a directory based on some criteria
	 * 
	 * @param string $dir - directory
	 * @param array $ext - extension of the file. If not specified, it will collect all the file details.
	 * @param array $exclude - specify the name of files to be excluded. eg: sampleFile.css, sample.js
	 */
    function get_files($dir, $ext = NULL, $exclude = NULL)
    {
    	$CI =& get_instance();
    	$CI->load->helper('file');

		if(!is_dir($dir))
		{
			return FALSE;	
		}
		
		$files = get_filenames($dir); //get all the files
		
		//Include only the files having specified extension
		if($ext)
		{
			$ext = array_map('strtolower', $ext); //lower all values in the $ext array
			
			foreach($files as $key => $value)
			{
				$file 	= pathinfo($value);
				$f_ext 	= strtolower($file['extension']);
				if(!in_array($f_ext, $ext))
				{
					unset($files[$key]);
				}
			}
		}
		
		//Exclude specified files from the list
		if($exclude)
		{
			$exclude = array_map('strtolower', $exclude); //lower all values in the $exclude array
			
			foreach($files as $key => $value)
			{
				$file 		= pathinfo($value);
				$filename 	= strtolower($file['basename']);
				if(in_array($filename, $exclude))
				{
					unset($files[$key]);
				}
			}
		}
		return $files;
    }   
}

if ( ! function_exists('load_css_files'))
{
	/**
	 * Load all CSS files placed in the CSS directory to the template.
	 * 
	 * @param array - exclude filenames
	 * @param string - css base directory path
	 */
    function load_css_files($exclude = array(), $css_base = NULL)
    {
    	$CI =& get_instance();
    	$CI->load->helper('file');

		$base_uri 	= $CI->config->item('base_url');
		$css_base 	= ($css_base) ? $css_base : $CI->config->item('css_dir');
		$css_dir 	= $base_uri.$css_base;
		
		//get the files
		$css_files = get_files($css_base, array('css'), $exclude);
		
		if(!empty($css_files))
		{
			foreach ($css_files as $css_file) 
			{
				echo "<link rel='stylesheet' href='{$css_dir}/{$css_file}' type='text/css' /> \n";
			}
			
		}	
    }   
}


if ( ! function_exists('load_js_files'))
{
	/**
	 * Load all javascript files placed in the JS directory to the template.
	 * 
	 * @param array - exclude filenames
	 * @param string - js base directory path
	 */
    function load_js_files($exclude = array(), $js_base = NULL)
    {
    	$CI =& get_instance();
    	$CI->load->helper('file');

    	$base_uri 	= $CI->config->item('base_url');
		$js_base 	= ($js_base) ? $js_base : $CI->config->item('js_dir');
		$js_dir 	= $base_uri.$js_base;
		
		//get the files
		$js_files = get_files($js_base,array('js'),$exclude);
		
		if(!empty($js_files))
		{
			foreach ($js_files as $js_file) 
			{
				echo "<script src='{$js_dir}/{$js_file}' type='text/javascript'></script> \n";
			}
			
		}	
    }   
}

if ( ! function_exists('add_script'))
{
	/**
	 * Add javascript.
	 * 
	 * @param string - filename
	 * @param string - js base directory path
	 */
	function add_script( $file, $js_base = NULL )
	{
		if( $file )
		{
			$CI =& get_instance();
			
			$base_uri 	= $CI->config->item('base_url');
			$js_base 	= ($js_base) ? $js_base : $CI->config->item('js_dir');
			$js_dir 	= $base_uri.$js_base;
			
			echo "<script src='{$js_dir}/{$file}' type='text/javascript'></script> \n";
		}
	}	
}

if ( ! function_exists('add_stylesheet'))
{
	/**
	 * Add stylesheet.
	 * 
	 * @param string - filename
	 * @param string - js base directory path
	 */
	function add_stylesheet( $file, $css_base = NULL )
	{
		if( $file )
		{
			$CI =& get_instance();
			
			$base_uri 	= $CI->config->item('base_url');
			$css_base 	= ($css_base) ? $css_base : $CI->config->item('css_dir');
			$css_dir 	= $base_uri.$css_base;
			
			echo "<link rel='stylesheet' href='{$css_dir}/{$file}' type='text/css' /> \n";
		}
	}	
}


if( ! function_exists('add_field'))
{
	function add_field($field, $type= NULL, $label = false)
	{
		$field['class'] = (isset($field['class'])) ? $field['class'] .' form-control' : 'form-control';
		$lbl_attr = array(
			// 'class'=>'col-lg-2 control-label'
		);
		
		//generate field label
		$label = ($label) ? $label : "";
		
		$field_html = "";
		$field_html .= "<div class='form-group'>";
		$field_html	.= form_label($label, $field['name'], $lbl_attr);
		// $field_html .= ( strtolower($type == "dropdown" && isset($field['btn_link'])) ) ? "<div class='col-lg-4'>" : "<div class='col-lg-10'>"; //drop-down is small
		
		//generate field
		switch ($type) 
		{
			case 'textarea':
				$field_html	.= form_textarea($field);
				break;
                //checkbox field generation added 
            case 'checkbox':
                $field_html    .= form_checkbox($field);
                break;
			case 'dropdown':
				$btn_link = false; //add button link
				$btn_txt = ""; //text for the button
				
				if( isset($field['btn_link']) )
				{
					$btn_link = $field['btn_link'];
					$btn_txt = $field['btn_txt'];
				}
				
				$field_html	.= form_dropdown($field['name'], $field['options'], $field['value'],'class="'.$field['class'].'"');
				
				if($btn_link)
				{
					$field_html .= "</div>";
					$field_html .= "<div class='col-lg-4'>";
					$field_html .= icon_anchor(base_url().$btn_link,$btn_txt, array(
										'class'=>'btn btn-success btn',
										'icon'=>'plus-sign',
										'title'=>'Add',
										'data-toggle'=>'modal'
									));
				}
				break;				
			case 'multiselect':
				$field_html	.= form_multiselect($field['name'], $field['options'], $field['value']);
				break;
			case 'password':
				$field_html	.= form_password($field);
				break;			    
			case 'input':
			default:
				$field_html	.= form_input($field);
				break;
		}
		
		// $field_html .= "</div>";
		
		if( form_error($field['name']) ) //set error for validation
		{
			$field_html .= "<div class='col-lg-12 f_error error'>";
			$field_html	.= form_error($field['name']);
			$field_html .= "</div>";
		}

		$field_html .= "</div>"; //end of <div class='form-group'>
		
		return $field_html;
	}
	
}

if( ! function_exists('input_field'))
{
	function input_field($field, $label = false)
	{
		return add_field($field, 'input', $label);
	}
}

if( ! function_exists('password_field'))
{
	function password_field($field, $label = false)
	{
		return add_field($field, 'password', $label);
	}
}

if( ! function_exists('input_checkbox'))
{
    function input_checkbox($field, $label = false)
    {
        return add_field($field, 'checkbox', $label);
    }
}
 
if( ! function_exists('textarea_field'))
{
	function textarea_field($field, $label = false)
	{
		return add_field($field, 'textarea', $label);
	}
}

if( ! function_exists('drop_down_field'))
{
	function drop_down_field($field, $label = false)
	{
		return add_field($field, 'dropdown', $label);
	}
}

if( ! function_exists('multi_select_field'))
{
	function multi_select_field($field, $label = false)
	{
		return add_field($field, 'multiselect', $label);
	}
}

if( ! function_exists('date_field'))
{
	function date_field($value = "", $label = "Date")
	{
		$value = ($value) ? date('d-m-Y',strtotime($value)) : "";
		$html  = '<div class="form-group">';
		$html .= '<label class="col-lg-2 control-label" for="date">'.$label.'</label>';
		$html .= '<div class="col-lg-3">
					<input type="text" name="date" class="form-control date" placeholder="DD-MM-YYYY" value="'.$value.'">
				  </div>';
		$html .= '</div>';
		return $html;
	}
}

if( ! function_exists('action_field'))
{
	/**
	 * Add action field with submit and reset button
	 * 
	 * @param $submit - submit button text
	 * @param $clear - reset button text
	 */
	function action_field($submit = "Submit", $cancel = "Cancel")
	{
		$html =	'<div class="form-group">
					<div class="action-field">
						<input type="submit" class="btn btn-primary" value="'.$submit.'" />
					</div>
				 </div>';
		return $html;		 
	}
}

if( ! function_exists('set_field'))
{
	/**
	 * Repopulate the submitted field values as set_value()
	 * only works with validated fields
	 * 
	 * @param $field - form field name
	 * @param $default - default value
	 */
	function set_field($field = '', $default = '')
	{
		$CI =& get_instance();
		
		return ($CI->input->post()) ? $CI->input->post($field) : $default;
	}
}

if( ! function_exists('is_checked'))
{
	/**
	 * Repopulate the submitted field values as set_value()
	 * 
	 * @param $field - form field name
	 * @param $default - default value
	 */
	function is_checked($field = '', $default = FALSE)
	{
		$CI =& get_instance();
		
		if( $CI->input->post() )
		{
			return ($CI->input->post($field));
		}
		else
		{
			return $default;
		}
	}
}

if( ! function_exists('radio_checked'))
{
	/**
	 * Repopulate the submitted field values as set_value()
	 * only works with radio buttons
	 * 
	 * @param $field - form field name
	 * @param $val - field value
	 * @param $default - default value
	 */
	function radio_checked($field = '', $val = '', $default = FALSE)
	{
		$CI =& get_instance();
		
		if( $CI->input->post() )
		{
			if($CI->input->post($field))
			{
				return ($val == $CI->input->post($field)) ? "checked='checked'" : ""; 
			}
			else 
			{
				return ($default) ? "checked='checked'" : "";
			}
		}
		else
		{
			return ($default) ? "checked='checked'" : "";
		}
	}
}

if( ! function_exists('icon_anchor'))
{
	/**
	 * Anchor Link with icons
	 *
	 * Creates an anchor based on the local URL and glyphicon.
	 *
	 * @access	public
	 * @param	string	the URL
	 * @param	string	the link title
	 * @param	mixed	any attributes
	 * @return	string
	 */
	function icon_anchor($uri = '', $title = '', $attributes = '')
	{
		$title = (string) $title;
		$icon_span = "";

		if ( ! is_array($uri))
		{
			$site_url = ( ! preg_match('!^\w+://! i', $uri)) ? site_url($uri) : $uri;
		}
		else
		{
			$site_url = site_url($uri);
		}

		if ($attributes != '')
		{
			if(isset($attributes['icon']))
			{
				$icon_span = '<span class="glyphicon glyphicon-'.$attributes['icon'].'"></span>';
				unset($attributes['icon']);
			}
			$attributes = _parse_attributes($attributes);
		}

		return '<a href="'.$site_url.'"'.$attributes.'>'.$icon_span.' '.$title.'</a>';
	}
}

if( ! function_exists('format_date'))
{
	/**
	 * get the date in user friendly format
	 * 
	 * @param $date - value from db 
	 * @return date mm-dd-yyyy
	 * 
	 */
	function format_date($date, $format="d-m-Y")
	{
		return (!empty($date) && $date != "0000-00-00") ? date($format, strtotime($date)) : "";
	}
}

if( ! function_exists('is_logged_in'))
{
	/**
	 * Is user logged in to the system
	 * 
	 */
	function is_logged_in()
	{
    	$CI =& get_instance();
    	$CI->load->model('admin_model','admin');
    	return $CI->admin->current_user();
	}
}

if( ! function_exists('app_config'))
{
	/**
	 * get config item
	 * 
	 */
	function app_config($key) {
		$CI =& get_instance();
		return $CI->config->item($key);
	}
}

if( ! function_exists('json_response'))
{
	/**
	 * array to json
	 * 
	 */
	function json_response($data = array()) {
		echo json_encode($data);
		exit;
	}
}


/* End of file admin_helper.php */
/* Location: ./application/helpers/admin_helper.php */