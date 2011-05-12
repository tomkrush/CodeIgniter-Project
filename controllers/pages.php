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
		$this->load->model('pages_model');
		$this->load->model('articles_model');

		$this->load->database();
		
		$blog = $this->blogs_model->first();

		$this->load->vars('blog', $blog);
		
		$this->load->view('index');
	}
}