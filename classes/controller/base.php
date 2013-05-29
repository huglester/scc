<?php

class Controller_Base extends Controller_Template
{
	public $current_user_id = null;
	public $current_user_name = null;

	public function before()
	{
		parent::before();

		// Check user and user rights
		if ( ! \Auth::has_access(Request::active()->controller.'.'.Request::active()->action))
		{
			//TODO Log ip and error count +1 + error type
			Session::set_flash('error', 'Sorry you don\'t have rights to do it.');
			return Response::redirect('accounts/login');
		}

		// Getting current user id
		list(, $this->current_user_id) = Auth::get_user_id();

		// Getting current user name
		$this->current_user_name = Auth::get('username');

		// Setting current user id and name to view
		View::set_global('current_user_id', $this->current_user_id, false);
		View::set_global('current_user_name', $this->current_user_name, false);
	}
}