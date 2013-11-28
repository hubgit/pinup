<?php

if (!defined('DATA_DIR')) {
	define('DATA_DIR', __DIR__ . '/web');
}

function new_file($data) {
	$slug = slug($data['name']);

	$year = date('Y');
	$i = 0;

	do {
		$suffix = $i ? '-' . $i : '';
		$file = DATA_DIR . '/' . $year . '/' . $slug . $suffix;
		$i++;
	} while (file_exists($file . '.json'));

	$dir = dirname($file);

	if (!file_exists($dir)) {
		mkdir($dir, 0777, true);
	}

	return $file;
}

function save_new_version($file, $data) {
	$previous = data_from_file($file . '.json');
	$previous['next-version'] = $data['version'];

	$data['dateModified'] = time();
	$data['version'] = $previous['version'] + 1;
	$data['previous-version'] = $previous['version'];

	save_data($file, $data);
	save_data($file, $previous);
}

function save_data($file, $data) {
	write_json($file . '.' . $data['version'] . '.json', $data);
	write_html($file . '.' . $data['version'] . '.html', $data);
}

function write_json($file, $data) {
	file_put_contents($file, json_encode($data)); // JSON_PRETTY_PRINT
	// TODO: set file modified timestamp = $data['dateModified']
}

function write_html($file, $data) {
	ob_start();
	require __DIR__ . '/templates/item.html.php'; // TODO: put template in the web directory?
	file_put_contents($file, ob_get_clean());
	// TODO: set file modified timestamp = $data['dateModified']
}

function create_symlinks($file, $data) {
	$formats = array('json', 'html');

	foreach ($formats as $format) {
		$path = $file . '.' . $format;

		if (file_exists($path)) {
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

function slug($text) {
    // Replace "'s" with "s"
    $text = preg_replace('/\'s(\s|\z)/', 's$1', $text);

    // Replace non-letters/non-digits with '-'
    $text = preg_replace('/[^\\pL\d]+/u', '-', $text);

    // Remove trailing hyphens
    $text = trim($text, '-');

    // Translate to ASCII
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

    // Lowercase
    $text = strtolower($text);

    // Remove non-hyphen/non-word characters
    $text = preg_replace('/[^-\w]+/', '', $text);

    // Truncate to maximum 50 characters, on a word boundary (hyphen) if possible
    $text = truncate_slug($text);

    // Fall-back to produce something
    return $text ?: 'untitled';
}

function truncate_slug($text, $max = 50, $min = 30) {
	while (strlen($text) > $max) {
	    // minimum length $min characters (strrpos finds the last occurrence of the string)
	    $separatorPosition = strrpos($text, '-', $min);

	    // if there isn't a word boundary between $min and $max characters, truncate at $max characters
	    if (!$separatorPosition) {
	        return substr($text, 0, $max);
	    }

	    $text = substr($text, 0, $separatorPosition);
	}

	return $text;
}

function iterate_files($callback) {
	$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(DATA_DIR));

	/* @var $iterator SplFileInfo[] */
	foreach ($iterator AS $file) {
	    if ($file->isFile() && $file->getExtension() === 'json') {
	    	$basename = $file->getBasename('.json');

	    	if ($basename == 'index') {
	    		continue;
	    	}

	    	// ignore version files
	    	if (!preg_match('/\.\d+$/', $basename)) {
	    		call_user_func($callback, $file->getPathname());
	    	}
	    }
	}
}

function build_index() {
	// TODO: load feed data from editable feed.json?
	$index = array(
		'title' => 'Index',
		'description' => 'A collection of items',
		'_items' => array()
	);

	// TODO: avoid loading them all into memory?
	iterate_files(function($file) use (&$index) {
		$index['_items'][] = data_from_file($file);
	});

	file_put_contents(DATA_DIR . '/index.json', json_encode($index));

	$e = error_reporting(E_ERROR);
	ob_start();
	require __DIR__ . '/templates/index.html.php'; // TODO: put template in the web directory?
	file_put_contents(DATA_DIR . '/index.html', ob_get_clean());
	error_reporting($e);
}

