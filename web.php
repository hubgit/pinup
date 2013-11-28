<?php

require __DIR__ . '/controller.php';

// TODO: .htpasswd for write permissions

switch ($_SERVER['REQUEST_METHOD']) {
	case 'POST':
		// TODO: set default properties from a schema.json?
		$data = data_from_file('php://input');
		$data['version'] = 1;
		$data['dateCreated'] = $data['dateModified'] = time();

		$file = new_file($data);
		save_data($file, $data);
		create_symlinks($file, $data);
		build_index();

		header('HTTP/1.1 201 Created');
		header('X-Location: /' . path_from_file($file));
		exit();

	case 'PUT':
		// TODO: look in the header to make sure that the version being edited is the latest version
		$data = data_from_file('php://input');
		$file = file_from_path($_GET['path']);
		save_new_version($file, $data);
		create_symlinks($file, $data);
		build_index();

		header('HTTP/1.1 200 OK');
		exit();

	case 'DELETE':
		$file = file_from_path($_GET['path']);
		$data = data_from_file($file);
		// TODO: set $data['deleted'] timestamp?
		rename($file . '.json', $file . '.deleted.json');
		rename($file . '.html', $file . '.deleted.html');
		// TODO: make sure that *.deleted.* gives a 404

		build_index();

		header('HTTP/1.1 204 No Content');
		exit();
}