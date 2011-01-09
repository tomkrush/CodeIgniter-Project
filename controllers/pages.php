<?php

// Yeah I know this controller was originally for pages
class Pages extends Controller 
{
	function __construct()
	{
		parent::Controller();	
	}
	
	function index()
	{		
		$this->load->model('blogs_model');
		$this->load->database();

		
		$blog = $this->blogs_model->create(array(
			'name' => 'test',
			'slug' => 'test'
		));
		
		// Array of errors if validated
		$errors = $this->blogs_model->errors;
		print_r($errors);

		if ( $blog )
		{
			print_r($blog->row());
		}		
	}
}