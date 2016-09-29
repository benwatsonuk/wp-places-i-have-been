<?php

/*
Plugin Name: WP Countries Visited
Plugin URI: http://benwatson.uk/countries-visited
Description: An easy way to display flags of the countries that you have visited.
Version: 1.0
Author: Ben Watson
Author URI: http://benwatson.uk
License: A "Slug" license name e.g. GPL2
*/

add_action('admin_menu', 'countries_visited_menu');

function countries_visited_menu() {
	add_menu_page('Countries Visited', 'Countries Visited', 'administrator', 'countries_visited_settings', 'countries_visited_settings_page', 'dashicons-flag');
}

add_action( 'admin_init', 'countries_visited_settings' );

function countries_visited_settings() {
	register_setting( 'countries_visited_settings_group', 'wp_countries_visited' );
}

function countries_visited_settings_page() { ?>
	<div class="wrap">
	<h2>Which countries have you been to?</h2>

	<form method="post" action="options.php">
    <?php settings_fields( 'countries_visited_settings_group' ); ?>
	<?php do_settings_sections( 'countries_visited_settings_group' ); ?>
	<?php
        $theData = theRegions();
		$theExistingData = maybe_unserialize(get_option('wp_countries_visited'));
		echo regionStructure('AS', $theData['AS'], $theExistingData);
		echo regionStructure('EU', $theData['EU'], $theExistingData);
		echo regionStructure('AF', $theData['AF'], $theExistingData);
		echo regionStructure('OC', $theData['OC'], $theExistingData);
		echo regionStructure('NA', $theData['NA'], $theExistingData);
		echo regionStructure('SA', $theData['SA'], $theExistingData);
		echo regionStructure('AN', $theData['AN'], $theExistingData);

    ?>

	<?php submit_button(); ?>

	</form>
	</div>
<?php }

function theRegions() {
	$countriesJSON = file_get_contents("data/countries.json", FILE_USE_INCLUDE_PATH);
	$regionsJSON = file_get_contents("data/regions.json", FILE_USE_INCLUDE_PATH);
	$regions = json_decode($regionsJSON, true);
	$countries = json_decode($countriesJSON, true);
	$unique_regions = array_unique($regions);
	$arr = [];
	foreach ($unique_regions as $k => $v) {
		$arr2 = [];
		foreach($regions as $k2 => $v2) {
			if($v == $v2 ) {
				$arr2[$k2] = $countries[$k2];
			}
		}
		asort($arr2);
		$arr[$v] = $arr2;
	}
	return $arr;
}

function regionStructure($region, $array, $existingData) {
	switch ($region) {
		case 'AS':
			$regionName = 'Asia';
        break;
		case 'EU':
			$regionName = 'Europe';
        break;
		case 'AF':
			$regionName = 'Africa';
			break;
		case 'NA':
			$regionName = 'North America';
		break;
		case 'OC':
			$regionName = 'Oceania';
		break;
		case 'SA':
			$regionName = 'South America';
		break;
		case 'AN':
			$regionName = 'Antarctica';
		break;
	}
	$regionStructure = '<fieldset><legend>'.$regionName.'</legend>';
	foreach($array as $k => $v) {
		($existingData["'$region'"]["'$k'"]) ? $been = true : $been = false;
		$regionStructure .= countryStructure($k, $v, $region, $been);
	}
	$regionStructure .= '</fieldset>';
	return $regionStructure;
}

function countryStructure($code, $name, $region, $been) {
	($been == true) ? $checked = 'checked="checked"' : $checked = '';
	$country = '<div class="country '.$code.'"><label for="'.$code.'">'.$name.'<img2 src="'.plugins_url("/flags/mini/".$code.".png", __FILE__).'" /></label><input type="checkbox" name="wp_countries_visited[\''.$region.'\'][\''.$code.'\']" id="'.$code.'" value="1" '.$checked.'></div>';
	return $country;
}



