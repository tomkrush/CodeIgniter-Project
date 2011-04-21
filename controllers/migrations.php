<?php
class Migrations extends CI_Controller 
{	
	function __construct()
	{
		parent::__construct();

		$this->load->database();
		$this->load->helper(array('directory','migrations_helper'));

		// Setup Migrations
		migrations_directory_setup();
	}
	
	function index()
	{	
		migration_up();
		
		echo 'Migrations run';
	}
	
	function created()
	{
		echo 'Migration Created';
	}
	
	function create($path)
	{
		create_migration($path);

		$CI =& get_instance();
		$CI->load->helper('url');
		redirect('migrations/created');
	}
}