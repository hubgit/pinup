<?php

define('DATA_DIR', __DIR__ . '/web');

// TODO: .htpasswd for write permissions
// TODO: generate index?

switch ($_SERVER['REQUEST_METHOD']) {
	case 'POST':
		$data = data_from_file('php://input');

		$year = date('Y');
		do {
			$path = sprintf('%d/%s', $year, uniqid());
			$file = DATA_DIR . '/' . $path;
		} while (file_exists($file . '.json'));

		$dir = dirname($file);

		if (!file_exists($dir)) {
			mkdir($dir, 0777, true);
		}

		$data['created'] = time(); // TODO: set 'updated' as well?
		$data['version'] = 1;

		save_data($file, $data);
		create_symlinks($file, $data);

		header('HTTP/1.1 201 Created');
		header('X-Location: /' . path_from_file($file));
		exit();

	case 'PUT':
		// TODO: look in the header to make sure that the version being edited is the latest version
		$data = data_from_file('php://input');

		$file = file_from_path($_GET['path']);

		$previous = data_from_file($file . '.json');
		$previous['next-version'] = $data['version'];

		$data['updated'] = time();
		$data['version'] = $previous['version'] + 1;
		$data['previous-version'] = $previous['version'];

		save_data($file, $data);
		save_data($file, $previous);
		create_symlinks($file, $data, true);

		header('HTTP/1.1 200 OK');
		exit();

	case 'DELETE':
		$file = file_from_path($_GET['path']);
		rename($file . '.json', $file . '.deleted.json');
		rename($file . '.html', $file . '.deleted.html');
		// TODO: make sure that *.deleted.* gives a 404

		header('HTTP/1.1 204 No Content');
		exit();
}

function save_data($file, $data) {
	$version = $data['version'];

	write_json($file . '.' . $version . '.json', $data);
	write_html($file . '.' . $version . '.html', $data);

	// TODO: set timestamps of files to $data['created'] or $data['updated']?
}

function write_json($file, $data) {
	file_put_contents($file, json_encode($data)); // JSON_PRETTY_PRINT
}

function write_html($file, $data) {
	ob_start();
	require __DIR__ . '/templates/item.html.php'; // TODO: put templates in the web directory?
	file_put_contents($file, ob_get_clean());
}

function create_symlinks($file, $data, $update = false) {
	$formats = array('json', 'html');

	foreach ($formats as $format) {
		$path = $file . '.' . $format;

		if ($update) {
			unlink($path);
		}

		symlink(basename($file) . '.' . $data['version'] . '.' . $format, $path);
	}
}

// TODO: handle_input negotiation (params)?
function data_from_file($file)
{
	$json = file_get_contents($file);
	$data = json_decode($json, true);

	if (!$data) {
		// TODO: more validation?
		header('HTTP/1.1 406 Not Acceptable');
	}

	return $data;
}

function file_from_path($path) {
	$file = DATA_DIR . '/' . $path;

	if (!file_exists($file . '.json')) {
		if (file_exists($file . '.deleted.json')) {
			header('HTTP/1.1 410 Gone'); // TODO: do this for GET requests to non-existent files
		} else {
			header('HTTP/1.1 404 Not Found');
		}

		exit();
	}

	return $file;
}

function path_from_file($file) {
	$parts = explode('/', $file);

	return implode('/', array_slice($parts, -2));
}

function h($text) {
	return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}
