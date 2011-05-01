<?php

class Blogs_Model extends My_Model 
{	
	function init()
	{
		$this->fields('rss_url', 'slug', 'name', 'description');
		$this->validates('name', array('presence' => TRUE, 'uniqueness' => TRUE));
		$this->validates('slug', array('presence' => TRUE, 'uniqueness' => array('exclude_self' => TRUE)));
		
		$this->has_many('articles');
		$this->before_save('transform_slug');
	}

	protected function transform_slug($values)
	{		
		if ( isset($values['slug']) )
		{
			$values['slug'] = strtolower(url_title($values['slug']));
		}
		
		return $values;		
	}
}