<?php

class Controller_Databases extends Controller_Base
{
	public function action_index()
	{
		try
		{
			// Preparing limit
			$default_limit	= 20;
			$valid_limits	= array(20, 40, 60, 100);
			$unvalid_limit	= Input::get('limit', Cookie::get('databases_limit', $default_limit));
			$valid_limit 	= (int)in_array($unvalid_limit, $valid_limits) ? $unvalid_limit : $default_limit;
			Cookie::set('databases_limit', $valid_limit);

			// Preparing current page
			$default_page 	= 1;
			$current_page	= (int)Input::get('page', Cookie::get('databases_page', $default_page));
			Cookie::set('databases_page', $current_page);

			// Preparing search
			$default_search = '';
			$current_search	= Input::post('search', Cookie::get('databases_search', $default_search));
			Cookie::set('databases_search', $current_search);

			// Preparing pagination
			$pagination = Pagination::forge('databases', array(
				'pagination_url'	=> Uri::current(),
				'per_page'			=> $valid_limit,
				'current_page'		=> $current_page,
				'uri_segment'		=> 'page',
				'total_items'		=> DB::select('id')->from('databases')
											->where('title', 'like',$current_search.'%')
											->execute()->count(),
			));

			// Preparing server configuration
			Config::load('server', 'server');

			// Getting databases
			try
			{
				$databases = Cache::get('databases.'.md5($valid_limit.'_'.$current_page.'_'.$current_search));
			}
			catch (\CacheNotFoundException $e)
			{
				// Getting databases
				$databases = Model_Database::find(function($query) use($pagination, $current_search)
				{
					$query->select('databases.id','databases.title', 'databases.password', 'databases.updated_at', 'databases.updated_at',
									'databases.created_at', 'accounts.first_name', 'accounts.last_name', 'databases.backuped_at', 'databases.restored_at')
							->where('title', 'like', $current_search.'%')
							->join('accounts')
							->on('databases.account_id', '=', 'accounts.id')
							->limit($pagination->per_page)
							->offset($pagination->offset);
					return $query;
				});

				// Save databases to cache
				Cache::set('databases.'.md5($valid_limit.'_'.$current_page.'_'.$current_search), $databases, 3600 * 3);
			}

			// Setting data to template
			$this->template->title = 'Databases';
			$this->template->content = View::forge('databases/index', array(
				'databases' => $databases,
				'pagination' => $pagination,
				'current_search' => $current_search,
				'server_name' => Config::get('server.name'),
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
			// Preparing validation
			$validation = Validation::forge();
			$validation->add_field('title', 'Title', 'required|trim|valid_string[lowercase,alpha]|min_length[2]|max_length[12]|unique[hosts.title]|unique[databases.title]');

			// Form was submitted
			if (Input::method() === 'POST')
			{
				// Check CSRF
				if ( ! Security::check_token())
				{
					//TODO Log ip and error count +1 + error type
					return Response::redirect('databases/create');
				}

				// Validate form
				if ($validation->run())
				{
					// Create database
					$database = Model_Database::forge();
					$database->title 		= strtolower($validation->validated('title'));
					$database->account_id	= $this->current_user_id;
					$database->active 		= 1;
					$database->password 	= Str::random('alnum', 8);

					if ($database->save())
					{
						// Trigger create database event
						Event::trigger('databases_create', array(
							'id' => $database->id,
							'title' => $database->title,
							'password' => $database->password,
						));

						// Setting messages and redirecting
						Session::set_flash('success', 'Database was successfully created!');
						return Response::redirect('databases/index');
					}
					else
					{
						//TODO Log ip and error count +1 + error type
						Log::error('Error - Critical Create hosts !!! in '.$e->getFile().' on line '.$e->getLine());
						return Response::redirect_back('/');
					}
				}
			}

			// Setting data to template
			$this->template->title = 'Crate';
			$this->template->content = View::forge('databases/create', array(
				'validation' => $validation,
			));
		}
		catch (Exception $e)
		{
			Log::error('Error - '.$e->getMessage().' in '.$e->getFile().' on line '.$e->getLine());
			return Response::redirect('/');
		}
	}

	// public function action_edit($id = null)
	// {
	// 	try
	// 	{
	// 		// Getting host
	// 		$host = Model_Host::find_by_pk((int)$id);

	// 		if ( ! $host)
	// 		{
	// 			// Setting error and log messages and redirecting to index
	// 			Session::set_flash('error', 'Unable to find host for editing with id '.$id);
	// 			Log::error('Unable to find host for editing with id '.$id);
	// 			return Response::redirect('hosts');
	// 		}

	// 		// Preparing validation
	// 		$validation = Validation::forge();
	// 		$validation->add_field('title', 'Title', 'required|trim|min_length[2]|max_length[15]|unique[hosts.title,'.$host->id.']');
	// 		$validation->add_field('password', 'Password', 'required|trim|min_length[6]|max_length[15]');

	// 		// Form was submitted
	// 		if (Input::method() === 'POST')
	// 		{
	// 			// Check CSRF
	// 			if ( ! Security::check_token())
	// 			{
	// 				//TODO Log ip and error count +1 + error type
	// 				return Response::redirect('hosts');
	// 			}

	// 			// Validate form
	// 			if ($validation->run())
	// 			{
	// 				// Updating account
	// 				$host->title = $validation->validated('title');
	// 				$host->password = $validation->validated('password');
	// 				$host->save();

	// 				// Trigger events
	// 				Event::trigger('hosts_edit', array(
	// 					'id' => $host->id,
	// 					'title' => $host->title,
	// 					'password' => $host->password,
	// 				));

	// 				// Setting success messages and redirecting
	// 				Session::set_flash('success', 'Host '.$validation->validated('title').' was updated !');
	// 				return Response::redirect('hosts');
	// 			}
	// 		}

	// 		// Setting data to template
	// 		$this->template->title = 'Edit';
	// 		$this->template->content = View::forge('hosts/edit', array(
	// 			'validation' => $validation,
	// 			'host'	 => $host,
	// 		));
	// 	}
	// 	catch (Exception $e)
	// 	{
	// 		Log::error('Error - '.$e->getMessage().' in '.$e->getFile().' on line '.$e->getLine());
	// 		return Response::redirect('/');
	// 	}
	// }

	public function action_delete($id = null)
	{
		try
		{
			// Getting database
			$database = Model_Database::find_by_pk((int)$id);

			if ($database)
			{
				// Deleting database
				$database->delete();

				// Trigger delete database event
				Event::trigger('databases_delete', array(
					'id' => $database->id,
					'title' => $database->title,
					'password' => $database->password,
				));

				// Setting success message
				Session::set_flash('success', 'Database '.$database->title.' was removed !');
			}
			else
			{
				Session::set_flash('error', 'Unable to find host with id : '.$id);
				Log::error('Unable to find host with id : '.$id);
			}

			// Redirecting to index
			return Response::redirect('databases');
		}
		catch (Exception $e)
		{
			Log::error('Error - '.$e->getMessage().' in '.$e->getFile().' on line '.$e->getLine());
			return Response::redirect('/');
		}
	}

	public function action_backup($id = null)
	{
		try
		{
			// Getting database
			$database = Model_Database::find_by_pk((int)$id);

			if ($database)
			{
				// Trigger database backup event
				Event::trigger('databases_backup', array(
					'id' => $database->id,
					'title' => $database->title,
					'password' => $database->password,
				));

				$database->backuped_at = time();
				$database->save();

				// Setting success message
				Session::set_flash('success', 'Database '.$database->title.' was backup-ed !');
			}
			else
			{
				Session::set_flash('error', 'Unable to find host with id : '.$id);
				Log::error('Unable to find host with id : '.$id);
			}

			// Redirecting to index
			return Response::redirect('databases');
		}
		catch (Exception $e)
		{
			Log::error('Error - '.$e->getMessage().' in '.$e->getFile().' on line '.$e->getLine());
			return Response::redirect('/');
		}
	}

	public function action_restore($id = null)
	{
		try
		{
			// Getting database
			$database = Model_Database::find_by_pk((int)$id);

			if ($database)
			{
				// Trigger database restore event
				Event::trigger('databases_restore', array(
					'id' => $database->id,
					'title' => $database->title,
					'password' => $database->password,
				));

				$database->restored_at = time();
				$database->save();

				// Setting success message
				Session::set_flash('success', 'Database '.$database->title.' was restored !');
			}
			else
			{
				Session::set_flash('error', 'Unable to find host with id : '.$id);
				Log::error('Unable to find host with id : '.$id);
			}

			// Redirecting to index
			return Response::redirect('databases');
		}
		catch (Exception $e)
		{
			Log::error('Error - '.$e->getMessage().' in '.$e->getFile().' on line '.$e->getLine());
			return Response::redirect('/');
		}
	}
}