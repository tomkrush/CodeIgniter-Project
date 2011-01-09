<?php
class Migrations extends Controller 
{	
	function __construct()
	{
		parent::controller();

		$this->load->database();
		$this->load->helper(array('directory','migrations_helper'));

		// Setup Migrations
		migrations_directory_setup();
	}
	
	function index()
	{			
		$path = APPPATH.'db/migrate/';
						
		// Schema Information
		create_schema_table_if_not_exists();
		$current_schema_version = schema_version();
		$new_schema_version = NULL;
				
		// Force files into numerical order
		$files = directory_map($path);
		sort($files);
						
		// Migrate each file
		foreach($files as $file)
		{
			$file_path = $path . $file;
		
			$class = explode('_', $file, 2);
			$new_schema_version = strtotime($class[0]);
				
			// Only execute a migration if it NEEDs to be done
			if ( $current_schema_version < $new_schema_version )
			{			
				$class = explode('.', $class[1]);
				$class = $class[0];
		
				require($file_path);
		
				$migration = new $class;
				$migration->up();
			}
		}
	
		if ( $new_schema_version > $current_schema_version) set_schema_version($new_schema_version);
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