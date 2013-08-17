<?php

class Controller_Accounts extends Controller_Base
{
	public function action_login()
	{
		try
		{
			// Preparing validation
			$validation = Validation::forge();
			$validation->add_field('email', 'Email', 'required|trim|valid_email');
			$validation->add_field('password', 'Password', 'required|trim|min_length[6]|max_length[15]');

			// Form was submitted
			if (Input::method() === 'POST')
			{
				// Check CSRF
				if ( ! Security::check_token())
				{
					//TODO Log ip and error count +1 + error type
					return Response::redirect('accounts/login');
				}

				// Validate form
				if ($validation->run())
				{
					// Try login user
					if (\Auth::login($validation->validated('email'), $validation->validated('password')))
					{
						// Getting user group
						list($driver, $group_id) = current(\Auth::get_groups());

						// Checking group
						if ($group_id == -1)
						{
							// Logout banned account
							\Auth::logout();

							// Setting messages and redirecting
							Session::set_flash('error', 'Your account is banned!');
							return Response::redirect('accounts/login');
						}
						else
						{	
							// Trigger login events
							list(, $current_user_id) = Auth::get_user_id();
							Event::trigger('accounts_login', array(
								'id' => $current_user_id
							));

							// Setting messages and redirecting
							Session::set_flash('success', 'Welcome back !');
							return Response::redirect('dashboard');
						}
					}
					else
					{
						//TODO Log ip and error count +1 + error type
						Session::set_flash('error', 'Account/Password combination don\'t found or incorrect');
						return Response::redirect('accounts/login');
					}
				}
			}

			// Setting data to template
			$this->template->title = 'Login';
			$this->template->content = View::forge('accounts/login', array(
				'validation' => $validation,
			));
		}
		catch (Exception $e)
		{
			Log::error('Error - '.$e->getMessage().' in '.$e->getFile().' on line '.$e->getLine());
			return Response::redirect('/');
		}
	}

	public function action_logout()
	{
		try
		{
			// Logout user
			\Auth::logout();

			// Trigger logout events
			Event::trigger('accounts_logout', array(
				'id' => $this->current_user_id,
			));

			// Setting messages and redirecting
			Session::set_flash('success', 'You have successfully logged out!');
			return Response::redirect('accounts/login');
		}
		catch (Exception $e)
		{
			Log::error('Error - '.$e->getMessage().' in '.$e->getFile().' on line '.$e->getLine());
			return Response::redirect('/');
		}
	}

	public function action_index()
	{
		try
		{
			// Preparing group names
			\Config::load('simpleauth', true);
			$groupnames = \Config::get('simpleauth.groups');

			// Preparing limit
			$default_limit	= 20;
			$valid_limits	= array(20, 40, 60, 100);
			$unvalid_limit	= Input::get('limit', Cookie::get('accounts_limit', $default_limit));
			$valid_limit 	= (int)in_array($unvalid_limit, $valid_limits) ? $unvalid_limit : $default_limit;
			Cookie::set('accounts_limit', $valid_limit);

			// Preparing current page
			$default_page 	= 1;
			$current_page	= (int)Input::get('page', Cookie::get('accounts_page', $default_page));
			Cookie::set('accounts_page', $current_page);

			// Preparing pagination
			$pagination = Pagination::forge('accounts', array(
				'pagination_url'	=> Uri::current(),
				'total_items'		=> DB::select('id')->from('accounts')->execute()->count(),
				'per_page'			=> $valid_limit,
				'current_page'		=> $current_page,
				'uri_segment'		=> 'page',
			));

			// Getting accounts
			try
			{
				$accounts = Cache::get('accounts.'.md5($valid_limit.'_'.$current_page));
			}
			catch (\CacheNotFoundException $e)
			{
				// Getting accounts
				$accounts = Model_Account::find(function($query) use($pagination)
				{
					return $query->limit($pagination->per_page)
									->offset($pagination->offset);
				});

				// Save accounts to cache
				Cache::set('accounts.'.md5($valid_limit.'_'.$current_page), $accounts, 3600 * 3);
			}

			// Setting data to template
			$this->template->title = 'Accounts';
			$this->template->content = View::forge('accounts/index', array(
				'accounts' => $accounts,
				'groupnames' => $groupnames,
				'pagination' => $pagination,
			));
		}
		catch (Exception $e)
		{
			Log::error('Error - '.$e->getMessage().' in '.$e->getFile().' on line '.$e->getLine());
			return Response::redirect('/');
		}
	}

	public function action_create()
	{
		try
		{
			// Preparing group names
			\Config::load('simpleauth', true);
			$groupnames = \Config::get('simpleauth.groups');

			// Preparing validation
			$validation = Validation::forge();
			$validation->add_field('username', 'Username', 'required|trim|min_length[3]|max_length[15]|unique[accounts.username]');
			$validation->add_field('first_name', 'First name', 'required|trim|min_length[3]|max_length[20]');
			$validation->add_field('last_name', 'last name', 'required|trim|min_length[3]|max_length[20]');
			$validation->add_field('email', 'Email', 'required|trim|valid_email|unique[accounts.email]');
			$validation->add_field('group', 'Group', 'required|trim');
			$validation->add_field('password', 'Password', 'required|trim|min_length[6]|max_length[15]|match_field[confirm_password]');
			$validation->add_field('confirm_password', 'Confirm password', 'required|trim|min_length[6]|max_length[15]|match_field[password]');

			// Form was submitted
			if (Input::method() === 'POST')
			{
				// Check CSRF
				if ( ! Security::check_token())
				{
					//TODO Log ip and error count +1 + error type
					return Response::redirect('accounts/register');
				}

				// Validate form
				if ($validation->run())
				{
					// Create user
					if ($new_account_id = \Auth::create_user_custom(
												$validation->validated('first_name'),
												$validation->validated('last_name'),
												$validation->validated('username'),
												$validation->validated('password'),
												$validation->validated('email'),
												$validation->validated('group')
					))
					{
						// Trigger logout events
						Event::trigger('accounts_create', array(
							'id' => $new_account_id,
						));

						// Setting messages and redirecting
						Session::set_flash('success', 'Accounts was successfully created!');
						return Response::redirect('accounts/index');
					}
					else
					{
						//TODO Log ip and error count +1 + error type
						Log::error('Error - Critical Create account !!! in '.$e->getFile().' on line '.$e->getLine());
						return Response::redirect_back('/');
					}
				}
			}

			// Setting data to template
			$this->template->title = 'Crate';
			$this->template->content = View::forge('accounts/create', array(
				'validation' => $validation,
				'groupnames' => $groupnames,
			));
		}
		catch (Exception $e)
		{
			Log::error('Error - '.$e->getMessage().' in '.$e->getFile().' on line '.$e->getLine());
			return Response::redirect('/');
		}
	}

	public function action_edit($id = null)
	{
		try
		{
			// Getting account
			$account = DB::select('*')->from('accounts')->where('id', (int)$id)->execute()->current();

			if ( ! $account)
			{
				// Setting error and log messages and redirecting to index
				Session::set_flash('error', 'Unable to find account for editing with id '.$id);
				Log::error('Unable to find account for editing with id '.$id);
				return Response::redirect('accounts');
			}

			// Preparing validation
			$validation = Validation::forge();
			$validation->add_field('username', 'Username', 'required|trim|min_length[3]|max_length[15]|unique[accounts.username,'.$account['id'].']');
			$validation->add_field('first_name', 'First name', 'required|trim|min_length[3]|max_length[20]');
			$validation->add_field('last_name', 'last name', 'required|trim|min_length[3]|max_length[20]');
			$validation->add_field('email', 'Email', 'required|trim|valid_email|unique[accounts.email,'.$account['id'].']');
			$validation->add_field('group', 'Group', 'required');

			// Form was submitted
			if (Input::method() === 'POST')
			{
				// Check CSRF
				if ( ! Security::check_token())
				{
					//TODO Log ip and error count +1 + error type
					return Response::redirect('accounts/register');
				}

				// Checking or we updating password
				if (Input::post('password', null))
				{
					$validation->add_field('password', 'Password', 'required|trim|min_length[6]|max_length[15]|match_field[confirm_password]');
					$validation->add_field('confirm_password', 'Confirm password', 'required');
				}

				// Validate form
				if ($validation->run())
				{
					// Updating account
					DB::update('accounts')->set(
						array(
							'first_name' => $validation->validated('first_name'),
							'last_name' => $validation->validated('last_name'),
							'username' => $validation->validated('username'),
							'email' => $validation->validated('email'),
							'group' => $validation->validated('group'),
							'updated_at' => Date::forge()->get_timestamp(),
						)
					)->where('id', $account['id'])->execute();

					// Updating password
					if (Input::post('password'))
					{
						\Auth::reset_password_custom($account['id'], $validation->validated('password'));
					}

					// Trigger events
					Event::trigger('accounts_edit', array(
						'id' => $account['id'],
					));

					// Setting success messages and redirecting
					Session::set_flash('success', 'Account '.$validation->validated('username').' was updated !');
					return Response::redirect('accounts');
				}
			}

			// Getting group names
			\Config::load('simpleauth', true);
			$groupnames = \Config::get('simpleauth.groups');

			// Setting data to template
			$this->template->title = 'Edit';
			$this->template->content = View::forge('accounts/edit', array(
				'validation' => $validation,
				'groupnames' => $groupnames,
				'account'	 => $account,
			));
		}
		catch (Exception $e)
		{
			Log::error('Error - '.$e->getMessage().' in '.$e->getFile().' on line '.$e->getLine());
			return Response::redirect('/');
		}
	}

	public function action_delete($id = null)
	{
		try
		{
			// Getting account
			$account = DB::select('*')->from('accounts')->where('id', (int)$id)->execute()->current();

			if ($account and $account['id'] != $this->current_user_id)
			{
				// Deleting account
				DB::delete('accounts')->where('id', $account['id'])->execute();

				// Trigger logout events
				Event::trigger('accounts_delete', array(
					'id' => $account['id'],
				));

				// Setting success message
				Session::set_flash('success', 'Account '.$account['username'].' was removed !');
			}
			elseif ($account and $account['id'] == $this->current_user_id)
			{
				// Setting error and log messages
				Session::set_flash('error', 'Unable to remove self!');
				Log::error('Unable to remove self! username '.$account['username']);
			}
			else
			{
				Session::set_flash('error', 'Unable to find account with id : '.$id);
				Log::error('Unable to find account with id : '.$id);
			}

			// Redirecting to index
			return Response::redirect('accounts');
		}
		catch (Exception $e)
		{
			Log::error('Error - '.$e->getMessage().' in '.$e->getFile().' on line '.$e->getLine());
			return Response::redirect('/');
		}
	}
}
