<?php

	global $wpdb;
	global $post;

	$spreadsheet_url = get_option('sheets_url');
	$cptslug = get_option('cpt_slug');
	
	$g_title = get_option('g_title');
	$g_content = get_option('g_content');
	$cpt_status = get_option('cpt_status');


	// Process Custom Fields
	$custom_fields_str = get_option('g_custom_fields');
	$custom_fields = explode(",",$custom_fields_str);
	// print_r($custom_fields);

	$g_custom_slugs_str = get_option('g_custom_slugs');
	$g_custom_slugs = explode(",",$g_custom_slugs_str);
	// print_r($g_custom_slugs);

	$q_cs_result = array_combine($custom_fields, $g_custom_slugs);

	$btn_status = "";
	if ($q_cs_result == false) {
		echo '<h3 style="color: red;">Number of Google Sheets columns and custom fields are not the same!</h3>';
		$btn_status = "disabled";
	}

	echo '<h1>Import</h1>';
	echo '<div class="upload-form">';
	echo '<p>To insert the posts into the database, hit the button.</p><br>';
	echo '<a class="button button-primary" '.$btn_status.' style="padding:1em 3em;border-color:green; background:green; height:auto;" href="'.$_SERVER["REQUEST_URI"].'&insert_'.$cptslug.'">Import Google Sheet</a>';
	echo '</div>';

	if (!isset($_GET["insert_".$cptslug])) return;


	/*-------------------------------------------------------------------------------------*/


	/* Post Insertion */

	$csv = array_map('str_getcsv', file($spreadsheet_url));
	array_walk($csv, function(&$a) use ($csv) {
		$a = array_combine($csv[0], $a);
	});
	array_shift($csv); # remove column header
	//print_r($csv);

	
	$args = array(
		'post_type' => $cptslug,
		'post_status' => 'publish',
		'posts_per_page' => -1,
	);
	$all_posts = get_posts( $args );


	// Check if the posts exist
	$ps_check = array();
	foreach ($all_posts as $p) {  
		$ps_check[] .= get_field('field_5d15343e37649', $p->ID);
	}


	echo "<div class='updated'>";


	foreach ($csv as $entry ) {

		if (!in_array($entry['ID'], $ps_check)) {

			echo $entry['ID'];

			if (empty($entry[$g_title])) continue;

			echo ', Project site: '.$entry[$g_title].' inserted into custom post type: '.$cptslug.' with status: '.$cpt_status;
			echo '</br>';  

			// Create the post
			global $user_ID;
			$new_post = array(
				'post_title' => $entry['ID'],
				'post_content' => $entry[$g_content],
				'post_status' => $cpt_status,
				'post_type' => $cptslug
			);
			$post_id = wp_insert_post($new_post);
			echo "<hr>";

			// Add Custom Fields
			foreach ($q_cs_result as $column => $field_key) {
				update_field($field_key, $entry[$column], $post_id);
		    	// add_post_meta($post_id, $field, $entry[$column], true);
			}

		}
		else {
			echo '<span style="color:red;">Already exists - '.$entry[$g_title].'</span>';
			echo '</br>';
		}

	}

	echo '<br>';
	echo '<span style="color:green;">Done!</span>';
	echo '</div>';

?>