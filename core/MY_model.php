<?php

class MY_Model extends CI_Model 
{
	var $table_name = '';
	var $timestamps = TRUE;
	var $fields = array();
	var $transient = array();
	var $primary_key = 'id';
	
	var $validates = FALSE;
	var $field_validations = array();
	var $errors = array();
	
	var $created_at_column_name = 'created_at';
	var $updated_at_column_name = 'updated_at';
	
	var $before_save = array();
	var $after_save = array();
	
	var $before_create = array();
	var $after_create = array();

	var $before_validation = array();
	var $after_validation = array();
	
	function __construct() 
	{
		parent::__construct();
		
		$this->init();
		
		$this->_tablename();
	}
	
	function init()
	{

	}
	
/*-------------------------------------------------
CALLBACKS
-------------------------------------------------*/
	function before_save($callback)
	{
		if ( method_exists($this, $callback) ) $this->before_save[] = $callback;
	}
	
	function after_save($callback)
	{
		if ( method_exists($this, $callback) ) $this->after_save[] = $callback;
	}
	
	function before_create($callback)
	{
		if ( method_exists($this, $callback) ) $this->before_create[] = $callback;
	}
	
	function after_create($callback)
	{
		if ( method_exists($this, $callback) ) $this->after_save[] = $callback;
	}
	
	function before_validation($callback)
	{
		if ( method_exists($this, $callback) ) $this->before_validation[] = $callback;
	}
	
	function after_validation($callback)
	{
		if ( method_exists($this, $callback) ) $this->after_validation[] = $callback;		
	}
	
/*-------------------------------------------------
VALIDATION
-------------------------------------------------*/	
	function validates($field, $validators)
	{
		$this->field_validations[$field] = $validators;
	}

	function validate($values)
	{
		$this->validates = TRUE;
		$this->errors = array();
		
		if ( count($this->field_validations) )
		{
			foreach($this->before_validation as $callback)
			{
				$temp_create = $this->$callback($create);
				$create = $temp_create ? $temp_create : $create;
			}
						
			foreach ( $this->field_validations as $field => $validators )
			{
				foreach($validators as $validator => $options)
				{
					$method_name = 'validate_'.strtolower($validator);
				
					if ( method_exists($this, $method_name) )
					{
						$value = isset($values[$field]) ? $values[$field] : NULL;
					
						$options = $options ? $options : TRUE;
					
						if ( ! $this->$method_name($field, $value, $options, $values) )
						{
							$this->validates = FALSE;
						}
					}
				}
			}
			
			foreach($this->after_validation as $callback)
			{
				$temp_create = $this->$callback($create);
				$create = $temp_create ? $temp_create : $create;
			}
		}
		
		return $this->validates;
	}

	function validate_presence($field, $value, $options)
	{
		if ( ! isset($value) || (isset($value) && $value == ''))
		{
			$this->errors[] = array($field, ucfirst($field).' is required');
			return FALSE;
		}
		
		return TRUE;
	}
	
	function validate_uniqueness($field, $value, $options, $values)
	{
		if ( isset($value) )
		{
			$scopes = isset($options['scope']) ? $options['scope'] : array();
			$scopes = is_string($scopes) ? array($options['scope']) : $scopes;

			$conditions = array($field => $value);
			
			foreach($scopes as $scope )
			{
				if ( isset($values[$scope]) ) $conditions[$scope] = $values[$scope];
			} 
			
			if ( isset($options['exclude_self']) && $options['exclude_self'] == TRUE )
			{
				if ( isset($values[$this->primary_key]) )
				{
					$conditions[$this->primary_key.' !='] = $values[$this->primary_key];
				}
			}
						
			if ( $this->exists($conditions) )
			{				
				$this->errors[] = array($field, ucfirst($field).' "'.$value.'" already exist');
		 		return FALSE;
			}
		}
		
		return TRUE;
	}
	
	function validate_length($field, $value, $options)
	{
		if ( isset($value) )
		{
			$minimum = isset($options['minimum']) ? $options['minimum'] : NULL;
			$maximum = isset($options['maximum']) ? $options['maximum'] : NULL;
			
			$validated = TRUE;
			
			if ( $minimum && strlen($value) <= $minimum )
			{
				$this->errors[] = array($field, ucfirst($field).' "'.$value.'" must be longer than '.$minimum.' characters');
				$validated = FALSE;
			}
			
			if ( $maximum && strlen($value) >= $maximum )
			{
				$this->errors[] = array($field, ucfirst($field).' "'.$value.'" must be shorter than '.$maximum.' characters');
				$validated = FALSE;
			}
			
			return $validated;
		}
		
		return TRUE;
	}
	
	public function validate_confirm($field, $value, $options, $values)
	{
		$confirm_field = "confirm_{$field}";
		
		if ( ! array_key_exists($confirm_field, $values) )
		{
			$this->errors[] = array($field, "Confirm {$value} is required");
			return FALSE;
		}
		
		$confirm = $values[$confirm_field];
		
		if ( isset($value, $confirm) )
		{
			if ( $value != $confirm )
			{
				$this->errors[] = array($field, ucfirst($field)." doesn't match confirmation");
				return FALSE;
			}
		}
		
		return TRUE;		
	}
	
	public function errors()
	{
		$errors = array();
		
		foreach($this->errors as $error)
		{
			$errors[] = $error[1];
		}
		
		return $errors;
	}
	
/*-------------------------------------------------
INITALIZERS
-------------------------------------------------*/
	function fields($fields)
	{
		$this->fields = func_get_args();
		
		if ( $this->primary_key && ! in_array($this->primary_key, $this->fields) )
		{
			$this->fields[] = $this->primary_key;
		}
	}
	
	function transient($fields)
	{
		$this->transient = func_get_args();
	}
	
	function tablename($table_name)
	{
		$this->table_name = $table_name;
	}
	
	function has_timestamps($bool)
	{
		$this->timestamps = $bool;
	}
	
	function _tablename()
	{
		if ( empty($this->table_name) )
		{
			$this->table_name = str_replace('_model', '', strtolower(get_class($this)));
		}

		return $this->table_name;
	}
	
/*-------------------------------------------------
WRITE FUNCTIONS
-------------------------------------------------*/	
	
	function create($values)
	{
		$create = array();
		
		foreach($this->fields as $field)
		{
			if ( array_key_exists($field, $values) )
			{
				$create[$field] = $values[$field];
			}
		}
	
		foreach($this->transient as $field)
		{
			if ( array_key_exists($field, $values) )
			{
				$create[$field] = $values[$field];
			}
		}
	
		if ( $this->validate($create) )
		{	
			if ( $this->timestamps )
			{
				$create[$this->created_at_column_name] = time();
				$create[$this->updated_at_column_name] = time();
			}

			// Callbacks
			$callbacks = $this->before_create + $this->before_save;

			foreach($callbacks as $callback)
			{
				$temp_create = $this->$callback($create);
				$create = $temp_create ? $temp_create : $create;
			}

			foreach($this->transient as $field)
			{
				if ( array_key_exists($field, $values) )
				{
					unset($create[$field]);
				}
			}

			// Create Row
			$this->db->insert($this->table_name, $create);
		

			// Get Row ID
			$id = $this->db->insert_id();

			// Callbacks
			$callbacks = $this->after_create + $this->after_save;
			foreach($callbacks as $callback) $this->$callback($id);
				
			return $this->first($id);
		}	
		
		return NULL;
	}

	function update($id, $values)
	{
		$update = array();
		
		foreach($this->fields as $field)
		{
			if ( array_key_exists($field, $values) )
			{
				$update[$field] = $values[$field];
			}
		}
		
		foreach($this->transient as $field)
		{
			if ( array_key_exists($field, $values) )
			{
				$update[$field] = $values[$field];
			}
		}
		
		$update[$this->primary_key] = $id;

		if ( $this->validate($update) )
		{
			if ( $this->timestamps )
			{
				$update[$this->updated_at_column_name] = time();
			}
	
			// Callbacks
			$callbacks = $this->before_save;
			foreach($callbacks as $callback)
			{
				$temp_update = $this->$callback($update);
				$update = $temp_update ? $temp_update : $update;
			}

			foreach($this->transient as $field)
			{
				if ( array_key_exists($field, $values) )
				{
					unset($update[$field]);
				}
			}		
		
			unset($update[$this->primary_key]);
		
			$this->db->update($this->table_name, $update, array($this->primary_key => $id));

			// Callbacks
			$callbacks = $this->after_save;
			foreach($callbacks as $callback) $this->$callback($id);	
		}
		
		return $this->first($id);		
	}

	function destroy($conditions = NULL)
	{
		$conditions = is_numeric($conditions) || ! is_assoc($conditions) ? array($this->primary_key => $conditions) : $conditions;	
		$conditions = is_array($conditions) ? $conditions : array();		
		
		$this->db->delete($this->table_name, $conditions);

		return true;
	}
	
/*-------------------------------------------------
FINDERS
-------------------------------------------------*/	
	function exists($conditions = NULL)
	{
		$conditions = is_numeric($conditions) || ! is_assoc($conditions) ? array($this->primary_key => $conditions) : $conditions;	
		$conditions = is_array($conditions) ? $conditions : array();

		$this->_find($conditions);
		
		return $this->db->count_all_results() ? TRUE : FALSE;		
	}	
	
	function count($conditions = NULL)
	{
		$conditions = is_array($conditions) ? $conditions : array();

		$this->_find($conditions);
		
		return $this->db->count_all_results();		
	}
	
	function first($conditions = NULL)
	{	
		$conditions = is_numeric($conditions) ? array($this->primary_key => $conditions) : $conditions;
		$conditions = is_array($conditions) ? $conditions : array();
				
		$this->_find($conditions);
		$this->db->order_by($this->primary_key.' ASC');
		$this->db->limit(1);

		return $this->db->get();
	}
	
	function last($conditions = NULL)
	{		
		$conditions = is_numeric($conditions) ? array($this->primary_key => $conditions) : $conditions;
		$conditions = is_array($conditions) ? $conditions : array();
		
		$this->_find($conditions);
		$this->db->order_by($this->primary_key.' DESC');
		$this->db->limit(1);

		return $this->db->get();
	}
	
	function all($conditions = NULL)
	{
		return $this->find($conditions, 1, 0);		
	}
	
	function find($conditions = array(), $page = 1, $limit = 10)
	{
		if ( is_array($conditions) && ! is_assoc($conditions) )
		{
			$ids = $conditions;
			$conditions = array();
			
			$conditions[$this->primary_key] = $ids;
		}
				
		$this->_find($conditions);
		
		if ( $limit > 0 )
		{
			if ( $limit && $page )
			{
				$this->db->limit($limit, ($page - 1) * $limit);
			} 
			else
			{
				$this->db->limit($limit, ($page - 1) * $limit);
			}
		}

		return $this->db->get();		
	}
	
	private function field_exists($field)
	{
		list($field) = explode(' ', $field); 
		
		$fields = $this->fields;
		$fields[] = $this->primary_key;
		
		if ( $this->timestamps )
		{
			$fields[] = $this->created_at_column_name;
			$fields[] = $this->updated_at_column_name;
		}
		
		return in_array($field, $fields);
	}
	
	function _find($conditions = array())
	{	
		if ( is_array($conditions) )
		{
			foreach($conditions as $key => $value)
			{
				if ( $this->field_exists($key) )
				{
					if ( is_array($value) )
					{
						$this->db->where_in($key, $value);
					}
					else
					{
						$this->db->where($key, $value);
					}
				}
			}
		}
		
		$this->db->from($this->table_name);
	}
}