<?php

// Yeah I know this controller was originally for pages
class Pages extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();	
	}
	
	function index()
	{		
		$this->load->model('blogs_model');
		$this->load->database();

		
		// $blog = $this->blogs_model->create(array(
		// 	'name' => 'test',
		// 	'slug' => 'test'
		// ));
		
		$blog = $this->blogs_model->first(8)->row();
		var_dump($blog->articles->first()->row()->contents);
		
		// // Array of errors if validated
		// $errors = $this->blogs_model->errors;
		// print_r($errors);
		// 
		// if ( $blog )
		// {
		// 	print_r($blog->row());
		// }		
	}
}