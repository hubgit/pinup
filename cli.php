<?php

require __DIR__ . '/controller.php';

switch ($argv[1]) {
	case 'rebuild':
		iterate_files(function($input) {
			$output = preg_replace('/\.json$/', '.html', $input);
			print "Rebuilding $output\n";
			write_html($output, data_from_file($input));
		});

		build_index();
		exit('Rebuilt' . "\n");

	default:
		exit('Unknown command: ' . $argv[1] . "\n");
}
