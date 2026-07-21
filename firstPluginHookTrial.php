<?php

/*
 * Plugin Name: The Trial
 * Description: Trying to create my first ever plugin with help from the official WordPress Plugin Documentation
 * Author: GRA
 */

function firstTry() {
	echo '<h2>My plugin did this Holy Moly !!</h2>';
}

add_action('wp_footer', 'firstTry');
