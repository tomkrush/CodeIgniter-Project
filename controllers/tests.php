<?php

require_once APPPATH."third_party/unit/unit.php";

class Tests extends CI_Controller 
{	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		$this->load->helper('directory');
		
		$suite = new UnitTestSuite;

		$this->load->config('unit');
		
		$paths = $this->config->item('test_paths');
		
		foreach($paths as $path)
		{
			$path = APPPATH.$path.'/';
			$map = directory_map($path);
		
			if ( is_array($map) )
			{
				foreach($map as $file)
				{
					require_once($path.$file);

					$class = str_replace('.php', '', $file);

					$suite->addTestCase($class);
				}
			}
		}

		$suite->run();
	}
}