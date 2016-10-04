<?php

/*
Plugin Name: WP Places I Have Been
Plugin URI: http://benwatson.uk/Places-I-Have-Been
Description: An easy way to display flags of the countries that you have visited on your Wordpress site.
Version: 1.0
Author: Ben Watson
Author URI: http://benwatson.uk
License: GPL2
*/

function my_enqueue($hook) {
	if ( 'toplevel_page_places_i_have_been_settings' != $hook ) {
		return;
	}
	wp_enqueue_style( 'places_i_have_been_styles', plugins_url("dist/main.css", __FILE__));
	wp_enqueue_script( 'places_i_have_been_script', plugins_url("dist/main.js", __FILE__), 'jQuery');
}
add_action( 'admin_enqueue_scripts', 'my_enqueue' );


add_action('admin_menu', 'places_i_have_been_menu');

function places_i_have_been_menu() {
	add_menu_page('Places I Have Been', 'Places I Have Been', 'administrator', 'places_i_have_been_settings', 'places_i_have_been_settings_page', 'dashicons-flag');
}

add_action( 'admin_init', 'places_i_have_been_settings' );

function places_i_have_been_settings() {
	register_setting( 'places_i_have_been_settings_group', 'wp_places_i_have_been' );
}

function places_i_have_been_settings_page() { ?>
	<div class="wrap">
	<h2>Which countries have you been to?</h2>
	<span id="totalCountries">You have been to <span class="counter"></span> countries!</span>
	<form method="post" action="options.php">
    <?php settings_fields( 'places_i_have_been_settings_group' ); ?>
	<?php do_settings_sections( 'places_i_have_been_settings_group' ); ?>
	<?php
        $theData = theRegions();
		$theExistingData = maybe_unserialize(get_option('wp_places_i_have_been'));
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
	$regionStructure = '<fieldset><legend>'.$regionName.'<span class="countryCount"></span></legend><div class="countryContainer">';
	foreach($array as $k => $v) {
		($existingData["'$region'"]["'$k'"]) ? $been = true : $been = false;
		$regionStructure .= countryStructure($k, $v, $region, $been);
	}
	$regionStructure .= '</div></fieldset>';
	return $regionStructure;
}

function countryStructure($code, $name, $region, $been) {
	($been == true) ? $checked = 'checked="checked"' : $checked = '';
	($been == true) ? $countryClass = 'checked' : $countryClass = '';

	$country = '<div class="country '.$code.' '.$countryClass.'"><label for="'.$code.'"><img src="'.plugins_url("/flags/".$code.".png", __FILE__).'" />'.$name.'</label><input type="checkbox" name="wp_places_i_have_been[\''.$region.'\'][\''.$code.'\']" id="'.$code.'" value="1" '.$checked.'></div>';
	return $country;
}

function show_places_i_have_been(){
	$theExistingData = maybe_unserialize(get_option('wp_places_i_have_been'));
	$arr = [];
	foreach($theExistingData as $k => $v) {
		foreach($v as $k2 => $v2) {
			array_push($arr, $k2);
		}
	}
	sort($arr);

	return outputStructure($arr);
}

function outputStructure($arr) {
	$structure = '<ul id="countriesOutput">';
	foreach ( $arr as $k ) {
		$countriesJSON = file_get_contents("data/countries.json", FILE_USE_INCLUDE_PATH);
		$countriesJSON = json_decode($countriesJSON);
		$k2 = str_replace('\'', '', $k);
		$countryName = $countriesJSON->$k2;
		$structure .= '<li><img src="'. plugins_url( "/flags/". str_replace('\'', '', $k) .".png", __FILE__ ) .'" alt="'.$countryName.'" data-toggle="tooltip" data-placement="top" title="'.$countryName.'" /></li>';
	}
	$structure .= '</ul>';
	return $structure;
}

add_shortcode( 'Places_I_Have_Been', 'show_places_i_have_been');