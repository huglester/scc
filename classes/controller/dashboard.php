<?php

class Controller_Dashboard extends Controller_Base
{
	public function action_index()
	{
		try
		{
			// Setting data to template
			$this->template->title = 'Dashboard';
			$this->template->content = View::forge('dashboard/index', array(
			));
		}
		catch (Exception $e)
		{
			Log::error('Error - '.$e->getMessage().' in '.$e->getFile().' on line '.$e->getLine());
			return Response::redirect('/');
		}
	}
}