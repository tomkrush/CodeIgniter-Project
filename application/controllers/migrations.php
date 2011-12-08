<?php

class Migrations extends CI_Controller 
{	
	function __construct()
	{
		parent::__construct();

		$this->load->database();
		$this->load->helper('jot_migrations');	
	}
	
	function index()
	{
		if (ENVIRONMENT == 'development' || IS_CLI)
		{
			$migrations = new JotMigrations();
			$migrations->up();
		}
		else
		{
			show_404();
		}
	}
	
	function reset()
	{		
		if (ENVIRONMENT == 'development' || IS_CLI)
		{
			$migrations = new JotMigrations();
			$migrations->reset(TRUE);
		}
		else
		{
			show_404();
		}
	}
	
	function create($path)
	{
		if (ENVIRONMENT == 'development' || IS_CLI)
		{
			$migrations = new JotMigrations();
			$migrations->create($path);
		}
		else
		{
			show_404();
		}
	}
}