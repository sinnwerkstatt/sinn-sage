<?php

namespace Roots\Sage\Assets;

function assets() {
	wp_enqueue_style('bootstrap_css', get_template_directory_uri().'/assets/styles/bootstrap.min.css', false, null);
	wp_enqueue_style('main_css', get_template_directory_uri().'/assets/styles/main.css', false, null);

	if (is_single() && comments_open() && get_option('thread_comments')) {
		wp_enqueue_script('comment-reply');
	}

	wp_deregister_script('jquery');
	wp_register_script('jquery', get_template_directory_uri().'/assets/scripts/jquery.min.js', false, '');
	wp_enqueue_script('jquery');

	wp_enqueue_script('bootstrap_js', get_template_directory_uri().'/assets/scripts/bootstrap.min.js', ['jquery'], null, true);
	wp_enqueue_script('main_js', get_template_directory_uri().'/assets/scripts/main.js', ['jquery'], null, true);
}
add_action('wp_enqueue_scripts', __NAMESPACE__ . '\\assets', 100);



/* Compile the .less on every access of logged in user. */
// Sources: https://github.com/oyejorge/less.php
if(is_user_logged_in()) {
	require_once( get_template_directory().'/lib/less/Less.php' );

	// input and output location
	$inputFile = get_template_directory().'/assets/styles/main.less';
	$outputFile = get_template_directory().'/assets/styles/main.css';

	try{
		$parser = new \Less_Parser();
		$parser->parseFile( $inputFile, "" );
		$css = $parser->getCss();
	}catch(\Exception $e){
		global $less_error_message;
		$less_error_message = $e->getMessage();

		add_action('admin_notices',  function() {
			global $less_error_message;
			?>
			<div class="error">
				<h3>Less error</h3>
				<pre><?php echo $less_error_message ?></pre>
			</div>
			<?php
		});
	}

	file_put_contents($outputFile, $css);
}
