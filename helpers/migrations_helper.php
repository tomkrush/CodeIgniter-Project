<?php

define('MIGRATION_TIMESTAMPS', 1);

function migrations_directory_setup()
{
	$path = APPPATH.'db/';
	if ( ! file_exists($path) )
	{
		mkdir($path);
	}

	$path = $path.'migrate';	
	if ( ! file_exists($path) )
	{
		mkdir($path);
	}
}

function create_migration($file) {
	$CI =& get_instance();
	$CI->load->helper('inflector');

	$path = APPPATH.'db/migrate/';
	
	$path .= date('YmdHis').'_'.$file.'.php';
	
	if ( ! file_exists($path) )
	{
		$class_name = str_replace(' ','_', ucwords(str_replace('_',' ', $file)));	
			
		$template = "<?php\nclass {$class_name}\n{\n\tfunction up()\n\t{\n\n\t}\n}";
	
		file_put_contents($path, $template);
	}
}

function create_schema_table_if_not_exists()
{
	create_table('schema_migrations', array(
		array('name' => 'version', 'type' => 'integer')
	), NULL, TRUE);		
}

function schema_created()
{
	$CI =& get_instance();
	
	return $CI->db->table_exists('schema_migrations');
}

function schema_version()
{
	$schema_created = schema_created();
	
	if ( $schema_created )
	{
		$CI =& get_instance();
	
		$row = $CI->db->select_max('version')->get('schema_migrations')->row();
		$version = $row ? $row->version : FALSE;		
	
		return $version;
	}
	
	return FALSE;
}

function migration_path()
{
	return APPPATH.'db/migrate/';
}

function local_migration_files()
{
	$path = APPPATH.'db/migrate/';
			
	// Force files into numerical order
	$files = directory_map($path);
	sort($files);
	
	return $files;
}

function migration_up()
{
	// Schema Information
	create_schema_table_if_not_exists();
	$current_schema_version = schema_version();
	$new_schema_version = NULL;

	$path = migration_path();

	$files = local_migration_files();
					
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

function local_migration_version()
{
	$files = local_migration_files();
	$last = end($files);

	$class = explode('_', $last, 2);
	return strtotime($class[0]);
}

function set_schema_version($version)
{	
	$CI =& get_instance();

	$CI->db->insert('schema_migrations', array('version' => $version));
}

function create_table($table_name, $columns = array(), $options = array(), $if_not_exists = FALSE)
{
	$CI =& get_instance();

	$driver = $CI->db->dbdriver ;


	$columns_sql = '';
	$options_sql = '';
	
	if ( isset($options['primary_key']) && $options['primary_key'] == TRUE || empty($options['primary_key']) )
	{
		$id_name = isset($options['primary_key']) ? $options['primary_key'] : 'id';
		$id = array('name' => $id_name, 'primary_key' => TRUE, 'NOT_NULL' => TRUE, 'AUTO_INCREMENT' => TRUE);		

		if ( $driver == 'pdo' ) unset($id['AUTO_INCREMENT']);

		array_unshift($columns, $id);
	}

	$create_columns = array();

	foreach($columns as $column)
	{
			if ( $column == MIGRATION_TIMESTAMPS )
			{
					$create_columns[] = array('name' => 'created_at', 'type' => 'integer', 'NOT_NULL' => TRUE);
					$create_columns[] = array('name' => 'updated_at', 'type' => 'integer', 'NOT_NULL' => TRUE);
			}
			else
			{
				$create_columns[] = $column;
			}
	}
	
	foreach($create_columns as $column)
	{	
			// Column Name
			if ( empty($column['name']) ) continue;
			$columns_sql .= "  `".$column['name']."`";

			// Column Type
			$type = isset($column['type']) ? $column['type'] : 'integer';
			$columns_sql .= " "._MigrationDataType($type);

			// NOT NULL
			$columns_sql .= isset($column['NOT_NULL']) ? " NOT NULL" : NULL;
			
			// AUTO INCREMENT
			$columns_sql .= isset($column['AUTO_INCREMENT']) ? " AUTO_INCREMENT" : NULL;

			// Close Column
			$columns_sql .= ",\n";
	}
	
	$columns_sql .= isset($id_name) ? "  PRIMARY KEY (`{$id_name}`)\n" : NULL;
	
	$options_sql .= $driver == 'pdo' ? NULL : " CHARSET=utf8";

	$if_not_exists_sql = NULL;

	if ( $if_not_exists )
	{
		$if_not_exists_sql = "IF NOT EXISTS";
	}

	
	$sql = "CREATE TABLE {$if_not_exists_sql} `{$table_name}` (\n{$columns_sql}) {$options_sql};\n\n";

	if ( ! $if_not_exists )
	{
		drop_table($table_name);
	}
	
	$CI->db->query($sql);
}

function rename_table($old_table_name, $new_table_name)
{
	$sql = "RENAME TABLE `{$old_table_name}` TO `{$new_table_name}`;\n\n";
	
	$CI =& get_instance();
	$CI->db->query($sql);
}

function drop_table($table_name)
{
	$sql = "DROP TABLE IF EXISTS `{$table_name}`\n\n";
	
	$CI =& get_instance();
	$CI->db->query($sql);
}

function create_column($table_name, $options = array())
{
	if ( empty($options['name']) ) return FALSE;
	
	$column_sql = '';
	
	$column_name = $options['name'];
	
	// Column Name
	$column_sql .= "`".$column_name."`";

	// Column Type
	$type = isset($options['type']) ? $options['type'] : 'integer';
	$column_sql .= " "._MigrationDataType($type);

	// DEFAULT
	$default = array_key_exists('default', $options) ? $options['default'] : NULL;
	if ( isset($default) ) $column_sql .= " DEFAULT '".$default.'\''; 

	// NOT NULL
	$column_sql .= isset($options['NOT_NULL']) ? " NOT NULL" : NULL;
	
	// AUTO INCREMENT
	$column_sql .= isset($options['AUTO_INCREMENT']) ? " AUTO_INCREMENT" : NULL;
	
	$sql = "ALTER TABLE `{$table_name}` ADD COLUMN {$column_sql};\n\n";
	
	$CI =& get_instance();
	$CI->db->query($sql);
}

function change_column($table_name, $target_column_name, $options)
{	
	$column_sql = '';
	
  $options['name'] = isset($options['name']) ? $options['name'] : $column_name;
	$column_name = $options['name'];
	
	// Column Name
	$column_sql .= "`".$column_name."`";

	// Column Type
	$type = isset($options['type']) ? $options['type'] : 'integer';
	$column_sql .= " "._MigrationDataType($type);

	// NOT NULL
	$column_sql .= isset($options['NOT_NULL']) ? " NOT NULL" : NULL;

	// DEFAULT
	$default = array_key_exists('default', $options) ? $options['default'] : NULL;
	if ( isset($default) ) $column_sql .= " DEFAULT '".$default.'\''; 
	
	// AUTO INCREMENT
	$column_sql .= isset($options['AUTO_INCREMENT']) ? " AUTO_INCREMENT" : NULL;
	
	$sql = "ALTER TABLE `{$table_name}` CHANGE `{$target_column_name}` {$column_sql};\n\n";
	
	$CI =& get_instance();
	$CI->db->query($sql);
}

function drop_column($table_name, $column_name)
{
	$sql = "ALTER TABLE `{$table_name}` DROP  `{$column_name}`\n\n";
	
	$CI =& get_instance();
	$CI->db->query($sql);
}

function _MigrationDataType($type)
{
	$CI =& get_instance();
	$driver = $CI->db->dbdriver ;
		
	switch($driver)
	{
		case 'pdo':
			if ( $type == 'binary' ) 					$type = 'blob';
			else if ( $type == 'boolean') 		$type = 'boolean';
			else if ( $type == 'date' ) 			$type = 'date';
			else if ( $type == 'datetime' ) 	$type = 'datetime';
			else if ( $type == 'decimal' ) 		$type = 'decimal';
			else if ( $type == 'float' ) 			$type = 'float';
			else if ( $type == 'integer' ) 		$type = 'integer';
			else if ( $type == 'string' ) 		$type = 'varchar(255)';
			else if ( $type == 'text' ) 			$type = 'text';
			else if ( $type == 'time' ) 			$type = 'datetime';
			else if ( $type == 'timestamp' ) 	$type = 'datetime';		
		break;
		
		default:
			if ( $type == 'binary' ) 					$type = 'blob';
			else if ( $type == 'boolean') 		$type = 'tinyint(1)';
			else if ( $type == 'date' ) 			$type = 'date';
			else if ( $type == 'datetime' ) 	$type = 'datetime';
			else if ( $type == 'decimal' ) 		$type = 'decimal';
			else if ( $type == 'float' ) 			$type = 'float';
			else if ( $type == 'integer' ) 		$type = 'int(11)';
			else if ( $type == 'string' ) 		$type = 'varchar(255)';
			else if ( $type == 'text' ) 			$type = 'text';
			else if ( $type == 'time' ) 			$type = 'time';
			else if ( $type == 'timestamp' ) 	$type = 'datetime';		
		break;
	}	
	
	return $type;
}