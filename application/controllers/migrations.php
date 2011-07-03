<?php

class Migrations extends CI_Controller 
{	
	function __construct()
	{
		parent::__construct();

		$this->load->database();
		$this->load->helper(array('directory','jot_migrations'));	
	}
	
	function index()
	{
		$migrations = new JotMigrations();
		$migrations->up();
	}
	
	function reset()
	{		
		$migrations = new JotMigrations();
		$migrations->reset(TRUE);
	}
	
	function create($path)
	{
		$migrations = new JotMigrations();
		$migrations->create($path);
	}
}