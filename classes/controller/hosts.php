<?php

class Controller_Hosts extends Controller_Base
{
	public function action_index()
	{
		try
		{
			// Preparing limit
			$default_limit	= 20;
			$valid_limits	= array(20, 40, 60, 100);
			$unvalid_limit	= Input::get('limit', Cookie::get('hosts_limit', $default_limit));
			$valid_limit 	= (int)in_array($unvalid_limit, $valid_limits) ? $unvalid_limit : $default_limit;
			Cookie::set('hosts_limit', $valid_limit);

			// Preparing current page
			$default_page 	= 1;
			$current_page	= (int)Input::get('page', Cookie::get('hosts_page', $default_page));
			Cookie::set('hosts_page', $current_page);

			// Preparing search
			$default_search = '';
			$current_search	= Input::post('search', Cookie::get('hosts_search', $default_search));
			Cookie::set('hosts_search', $current_search);

			// Preparing pagination
			$pagination = Pagination::forge('hosts', array(
				'pagination_url'	=> Uri::current(),
				'per_page'			=> $valid_limit,
				'current_page'		=> $current_page,
				'uri_segment'		=> 'page',
				'total_items'		=> DB::select('id')->from('hosts')
											->where('title', 'like',$current_search.'%')
											->execute()->count(),
			));

			// Preparing server configuration
			Config::load('server', 'server');

			// Getting hosts
			try
			{
				$hosts = Cache::get('hosts.'.md5($valid_limit.'_'.$current_page.'_'.$current_search));
			}
			catch (\CacheNotFoundException $e)
			{
				// Getting hosts
				$hosts = Model_Host::find(function($query) use($pagination, $current_search)
				{
					$query->select('hosts.id','hosts.title', 'hosts.password', 'hosts.updated_at', 'hosts.updated_at',
									'hosts.created_at', 'accounts.first_name', 'accounts.last_name')
							->where('title', 'like', $current_search.'%')
							->join('accounts')
							->on('hosts.account_id', '=', 'accounts.id')
							->limit($pagination->per_page)
							->offset($pagination->offset);
					return $query;
				});

				// Save hosts to cache
				Cache::set('hosts.'.md5($valid_limit.'_'.$current_page.'_'.$current_search), $hosts, 3600 * 3);
			}

			// Setting data to template
			$this->template->title = 'hosts';
			$this->template->content = View::forge('hosts/index', array(
				'hosts' => $hosts,
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
			$validation->add_field('title', 'Title', 'required|trim|valid_string[lowercase,alpha]|min_length[2]|max_length[12]|unique[hosts.title]');

			// Form was submitted
			if (Input::method() === 'POST')
			{
				// Check CSRF
				if ( ! Security::check_token())
				{
					//TODO Log ip and error count +1 + error type
					return Response::redirect('hosts/create');
				}

				// Validate form
				if ($validation->run())
				{
					// Create host
					$host = Model_Host::forge();
					$host->title 		= strtolower($validation->validated('title'));
					$host->account_id	= $this->current_user_id;
					$host->active 		= 1;
					$host->password 	= Str::random('alnum', 8);

					if ($host->save())
					{
						// Trigger logout events
						Event::trigger('hosts_create', array(
							'id' => $host->id,
							'title' => $host->title,
							'password' => $host->password,
						));

						// Setting messages and redirecting
						Session::set_flash('success', 'Hosts was successfully created!');
						return Response::redirect('hosts/index');
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
			$this->template->content = View::forge('hosts/create', array(
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
			// Getting host
			$host = Model_Host::find_by_pk((int)$id);

			if ($host)
			{
				// Deleting host
				$host->delete();

				// Trigger delete host events
				Event::trigger('hosts_delete', array(
					'id' => $host->id,
					'title' => $host->title,
					'password' => $host->password,
				));

				// Setting success message
				Session::set_flash('success', 'Host '.$host->title.' was removed !');
			}
			else
			{
				Session::set_flash('error', 'Unable to find host with id : '.$id);
				Log::error('Unable to find host with id : '.$id);
			}

			// Redirecting to index
			return Response::redirect('hosts');
		}
		catch (Exception $e)
		{
			Log::error('Error - '.$e->getMessage().' in '.$e->getFile().' on line '.$e->getLine());
			return Response::redirect('/');
		}
	}
}