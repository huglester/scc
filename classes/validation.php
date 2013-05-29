<?php

class Validation extends \Fuel\Core\Validation
{
	public static function _validation_unique($val, $options, $id = 0)
	{
		list($table, $field) = explode('.', $options);
		$result = DB::select("LOWER (\"$field\")")
		->where($field, '=', Str::lower($val))
		->where('id', '!=', $id)
		->from($table)->execute();

		\Validation::active()->set_message('unique', 'The field :label must be unique, but :value has already been used');

		return ! ($result->count() > 0);
	}
}