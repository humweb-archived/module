<?php namespace App\Modules\Example\Controllers;


use BaseController, View;
class ExampleController extends BaseController {

	public function getIndex()
	{
		return View::make('example::index');
	}

}