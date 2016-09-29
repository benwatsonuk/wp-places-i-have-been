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
	register_setting( 'countries_visited_settings_group', 'accountant_name' );
	register_setting( 'countries_visited_settings_group', 'accountant_phone' );
	register_setting( 'countries_visited_settings_group', 'accountant_email' );
}

function countries_visited_settings_page() { ?>
	<div class="wrap">
	<h2>Which countries have you been to?</h2>

	<form method="post" action="options.php">
    <?php settings_fields( 'countries_visited_settings_group' ); ?>
	<?php do_settings_sections( 'countries_visited_settings_group' ); ?>
	<?php
        $theData = theRegions();
		echo regionStructure('AS', $theData['AS']);
		echo regionStructure('EU', $theData['EU']);
		echo regionStructure('AF', $theData['AF']);
		echo regionStructure('OC', $theData['OC']);
		echo regionStructure('NA', $theData['NA']);
		echo regionStructure('SA', $theData['SA']);
		echo regionStructure('AN', $theData['AN']);

    ?>

<!--	<table class="form-table">-->
<!--		<tr valign="top">-->
<!--			<th scope="row">Accountant Name</th>-->
<!--			<td><input type="text" name="accountant_name" value="--><?php //echo esc_attr( get_option('accountant_name') ); ?><!--" /></td>-->
<!--		</tr>-->
<!---->
<!--		<tr valign="top">-->
<!--			<th scope="row">Accountant Phone Number</th>-->
<!--			<td><input type="text" name="accountant_phone" value="--><?php //echo esc_attr( get_option('accountant_phone') ); ?><!--" /></td>-->
<!--		</tr>-->
<!---->
<!--		<tr valign="top">-->
<!--			<th scope="row">Accountant Email</th>-->
<!--			<td><input type="text" name="accountant_email" value="--><?php //echo esc_attr( get_option('accountant_email') ); ?><!--" /></td>-->
<!--		</tr>-->
<!--	</table>-->

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

function regionStructure($region, $array = null) {
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
	$region = '<fieldset><legend>'.$regionName.'</legend>';
	foreach($array as $k => $v) {
		$region .= countryStructure($k, $v);
	}
	$region .= '</fieldset>';
	return $region;
}

function countryStructure($code, $name) {
	$country = '<div class="country '.$code.'"><label for="'.$code.'">'.$name.'<img src="'.plugins_url("/flags/mini/".$code.".png", __FILE__).'" /></label><input type="checkbox" name="'.$code.'" id="'.$code.'" value="1"></div>';
	return $country;
}



