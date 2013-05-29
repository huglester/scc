<?php

class Auth_Login_Simpleauth extends \Auth\Auth_Login_Simpleauth
{
	public function create_user_custom($first_name, $last_name, $username, $password, $email, $group = 1, $profile_fields = array())
	{
		$password = trim($password);
		$email = filter_var(trim($email), FILTER_VALIDATE_EMAIL);
		$first_name = trim($first_name);
		$last_name = trim($last_name);

		if (empty($username) or empty($password) or empty($email))
		{
			throw new \SimpleUserUpdateException('Username, password or email address is not given, or email address is invalid', 1);
		}

		$same_users = \DB::select_array(\Config::get('simpleauth.table_columns', array('*')))
			->where('username', '=', $username)
			->or_where('email', '=', $email)
			->from(\Config::get('simpleauth.table_name'))
			->execute(\Config::get('simpleauth.db_connection'));

		if ($same_users->count() > 0)
		{
			if (in_array(strtolower($email), array_map('strtolower', $same_users->current())))
			{
				throw new \SimpleUserUpdateException('Email address already exists', 2);
			}
			else
			{
				throw new \SimpleUserUpdateException('Username already exists', 3);
			}
		}

		$user = array(
			'username'        => (string) $username,
			'password'        => $this->hash_password((string) $password),
			'email'           => $email,
			'first_name'      => $first_name,
			'last_name'       => $last_name,
			'group'			  => (int) $group,
			'profile_fields'  => serialize($profile_fields),
			'last_login'      => 0,
			'login_hash'      => '',
			'updated_at'	  => 0,
			'created_at'      => \Date::forge()->get_timestamp()
		);
		$result = \DB::insert(\Config::get('simpleauth.table_name'))
			->set($user)
			->execute(\Config::get('simpleauth.db_connection'));

		return ($result[1] > 0) ? $result[0] : false;
	}

	public function reset_password_custom($id, $password)
	{
		$password_hash = $this->hash_password($password);

		$affected_rows = \DB::update(\Config::get('simpleauth.table_name'))
			->set(array('password' => $password_hash))
			->where('id', '=', $id)
			->execute(\Config::get('simpleauth.db_connection'));

		if ( ! $affected_rows)
		{
			throw new \SimpleUserUpdateException('Failed to reset password, user was invalid.', 8);
		}

		return $affected_rows;
	}
}