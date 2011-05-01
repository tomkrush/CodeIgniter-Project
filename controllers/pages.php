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
	}
}