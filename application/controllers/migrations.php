<?php

class Migrations extends CI_Controller 
{	
	function __construct()
	{
		parent::__construct();

		$this->load->database();
		$this->load->helper('jot_migrations');	
	}
	
	public function _remap($method, $params = array())
	{
	    if (method_exists($this, $method) && (ENVIRONMENT == 'development' || IS_CLI))
	    {
	        return call_user_func_array(array($this, $method), $params);
	    }
	    
	    show_404();
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