<?php
return array(
	'container_prefix' => 'modules.',

	//Modules namespace
	'namespace' => 'App\Modules',

	//Modules path
	'path' => 'app/Modules',

	'driver' => 'database', // database, file, array
	
	'storage_path' => '',

	//DatabaseStorage
	'db' => array(
		'connection' => 'mysql',
		'table' => 'modules',
	)
);
