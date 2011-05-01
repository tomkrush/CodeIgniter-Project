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
			$tests_path = $path.'order.php';
			
			if (file_exists($tests_path))
			{
				require $tests_path;
			}
			else
			{
				$order = directory_map($path);
			}
		
			if ( is_array($order) )
			{
				foreach($order as $file)
				{
					if ($file == 'tests.php') continue;
					
					require_once($path.$file.'.php');

					$class = str_replace('.php', '', $file);

					$suite->addTestCase($class);
				}
			}
		}

		$suite->run();
	}
}