<?php

if ( ! function_exists('is_assoc'))
{
	function is_assoc($array) {
	    return (is_array($array) && (count($array)==0 || 0 !== count(array_diff_key($array, array_keys(array_keys($array))) )));
	}
}

if ( ! function_exists('rotate'))
{
	function rotate($source_array, $keep_keys = TRUE)
	{
		$new_array = array();
		foreach ($source_array as $key => $value)
		{
			$value = ($keep_keys === TRUE) ? $value : array_values($value);

			foreach ($value as $k => $v)
			{
				$new_array[$k][$key] = $v;
			}
		}

		return $new_array;
	}
}

if ( ! function_exists('element'))
{
	function element($keys, $array, $default = FALSE)
	{
		$array = (array)$array;
		
		if (empty($array))
			return $default;

		// Prepare for loop
		$keys = explode('.', $keys);

		do
		{
			// Get the next key
			$key = array_shift($keys);

			if (isset($array[$key]))
			{
				if (is_array($array[$key]) AND ! empty($keys))
				{
					// Dig down to prepare the next loop
					$array = $array[$key];
				}
				else
				{
					// Requested key was found
					return $array[$key];
				}
			}
			else
			{
				// Requested key is not set
				break;
			}
		}
		while ( ! empty($keys));

		return $default;
	}
}