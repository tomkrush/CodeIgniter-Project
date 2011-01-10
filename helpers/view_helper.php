<?php

if ( ! function_exists('render_items'))
{
	function render_items($name, $items) 
	{
		$CI =& get_instance();
		
		foreach($items as $item)
		{
			echo $CI->load->view('_'.$name, array($name => $item), TRUE)."\n";
		}
	}
}