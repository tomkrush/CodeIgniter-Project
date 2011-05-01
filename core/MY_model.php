<?php

class MY_Model extends CI_Model 
{
	protected $table_name = '';
	protected $timestamps = TRUE;
	protected $fields = array();
	protected $transient = array();
	protected $primary_key = 'id';

	protected $validates = FALSE;
	protected $field_validations = array();
	protected $errors = array();

	protected $created_at_column_name = 'created_at';
	protected $updated_at_column_name = 'updated_at';

	protected $before_save = array();
	protected $after_save = array();

	protected $before_create = array();
	protected $after_create = array();
   
	protected $before_validation = array();
	protected $after_validation = array();
	
	protected $relationships = array('has_many' => array(), 'has_one' => array(), 'belongs_to' => array());
	protected $relationship_vars = array();
	protected $base_filter = null;
	protected $base_join = null;
	protected $row = null;
	protected $is_row = false;
	
	function __get($var) 
	{
		$CI =& get_instance();
		
		if (isset($CI->$var)) return $CI->$var;
		
		//check if var is a relationship var, if so create object
		if ($this->is_row && in_array($var, $this->relationship_vars))
		{
			$pluralObject   = plural(strtolower($var));
			$singularObject = singular(strtolower($var));
			
			if (array_key_exists($var, $this->relationships['has_many']))
			{
				if (!isset($this->$pluralObject))
				{
					$modelName = ucwords($pluralObject).'_Model';
					
					//load model
					$this->load->model($modelName);
					
					//create instance of model
					$this->$pluralObject = clone $this->$modelName;
					
					if (isset($this->relationships['has_many'][$var]['through'])) {
						//create join and base_filter
						$this->$pluralObject->set_base_join($this->relationships['has_many'][$var]['through'].' t', 't.'.$singularObject.'_id = '.$pluralObject.'.id');
						$this->$pluralObject->set_base_filter(array(singular($this->table_name).'_id' => $this->row->id));
					}
					else {
						//create base_filter
						$this->$pluralObject->set_base_filter(array(singular($this->table_name).'_id' => $this->row->id));
					}
				}
				return $this->$pluralObject;
			}
			elseif (array_key_exists($var, $this->relationships['has_one']))
			{
				if (!isset($this->$singularObject))
				{
					$modelName = ucwords($pluralObject).'_Model';
					
					//load model
					$this->load->model($modelName);
					
					//create instance of model and base_filter
					$this->$singularObject = clone $this->$modelName;
					$this->$singularObject->set_row($this->$singularObject->first(array(singular($this->table_name).'_id' => $this->row->id))->row());
				}
				return $this->$singularObject;
			}
			elseif (array_key_exists($var, $this->relationships['belongs_to']))
			{
				if (!isset($this->$singularObject))
				{
					$modelName = ucwords($pluralObject).'_Model';
					
					//load model
					$this->load->model($modelName);
					
					//create instance of model and base_filter
					$this->$singularObject = clone $this->$modelName;
					$field = $singularObject.'_id';
					$this->$singularObject->set_row($this->$singularObject->first(array('id' => $this->row->$field))->row());
				}
				return $this->$singularObject;
			}
		}
		elseif ($this->is_row && isset($this->row->$var)) {
			return $this->row()->$var;
		}
		elseif ($this->is_row && isset($this->row->row) && isset($this->row->row->$var)) {
			return $this->row->row->$var;
		}
		return true;
	}
	
	function __isset($var)
	{
		if ($this->is_row && isset($this->row->$var)) {
			return true;
		}
		elseif ($this->is_row && isset($this->row->row) && isset($this->row->row->$var)) {
			return true;
		}
		return false;
	}
	
	function __construct() 
	{
		parent::__construct();
		
		$this->init();
		
		$this->_tablename();
		
		$this->load->helper('inflector');
	}
	
	function init()
	{

	}
	
/*-------------------------------------------------
RELATIONSHIPS
-------------------------------------------------*/
	function has_many($object, $settings = array())
	{
		$this->relationships['has_many'][$object] = $settings;
		
		$this->relationship_vars[] = plural($object);
	}
	function has_one($object, $settings = array())
	{
		$this->relationships['has_one'][$object] = $settings;
		
		$this->relationship_vars[] = singular($object);
	}
	function belongs_to($object, $settings = array())
	{
		$this->relationships['belongs_to'][$object] = $settings;
		
		$this->relationship_vars[] = singular($object);
	}
	function set_base_filter($conditions)
	{
		if (is_array($conditions) === false) return;
		$this->base_filter = $conditions;
	}
	function set_base_join($table, $on)
	{
		$this->base_join = array($table, $on);
	}

/*-------------------------------------------------
ROW STUFF
-------------------------------------------------*/
	function set_row($row)
	{
		$this->is_row = true;
		$this->row = $row;
	}
	function row()
	{
		if ($this->is_row) return $this->row;
		return;
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
	function exists($conditions = array())
	{
		$conditions = is_numeric($conditions) || ! is_assoc($conditions) ? array($this->primary_key => $conditions) : $conditions;	
		$conditions = is_array($conditions) ? $conditions : array();

		$this->_find($conditions);
		
		return $this->db->count_all_results() ? TRUE : FALSE;		
	}	
	
	function count($conditions = array())
	{
		$conditions = is_array($conditions) ? $conditions : array();

		$this->_find($conditions);

		return $this->db->count_all_results();		
	}
	
	function first($conditions = array())
	{
		$this->db->order_by($this->primary_key.' ASC');
		$this->db->limit(1);
		return $this->find($conditions);
	}
	
	function last($conditions = array())
	{
		$this->db->order_by($this->primary_key.' DESC');
		$this->db->limit(1);
		return $this->find($conditions);
	}
	
	function all($conditions = array())
	{
		return $this->find($conditions ? $conditions : array(), 1, 0);		
	}
	
	function find($conditions = array(), $page = 1, $limit = 10)
	{
		if ( is_array($conditions) && ! is_assoc($conditions) )
		{
			$ids = $conditions;
			$conditions = array();
			
			$conditions[$this->primary_key] = $ids;
		}
		
		if ($this->base_filter !== null)
		{
			$conditions = array_merge($this->base_filter, $conditions);
		}
		if ($this->base_join !== null)
		{
			$this->db->join($this->base_join[0], $this->base_join[1]);
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

		$r = $this->db->get();
		$r->result_object();
		for ($i=0, $len=count($r->result_object); $i<$len; $i++)
		{
			$obj = clone $this;
			$obj->set_row($r->result_object[$i]);
			$r->result_object[$i] = $obj;
		}

		return $r;		
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