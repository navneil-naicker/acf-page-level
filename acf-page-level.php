<?php
/**
	Plugin Name: ACF Page Level
	Plugin URI: http://www.navz.me/plugins/wp-pages-advanced
	Description: Sometimes you want to show the field groups based on the page level right? Well here you go. Enjoy!
	Version: 1.0.0
	Author: Navneil Naicker
	Author URI: http://www.navz.me
	License: GPLv2 or later
	
	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
	
	Copyright 2016 Navneil Naicker

*/

//Preventing from direct access
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class acf_page_level{
	
	public function __construct(){
		//Page Ancestor rules and match
		add_filter('acf/location/rule_values/page_ancestor', [$this, 'acf_location_rules_values_page_ancestor']);
		add_filter('acf/location/rule_match/page_ancestor', [$this, 'acf_location_rules_match_page_ancestor'], 10, 3);
		//Page Level rules and match
		add_filter('acf/location/rule_types', [$this, 'acf_location_rules_types']);
		add_filter('acf/location/rule_values/page_level', [$this, 'acf_location_rules_values']);
		add_filter('acf/location/rule_types', [$this, 'acf_location_rules_page_ancestor']);
		add_filter('acf/location/rule_match/page_level', [$this, 'acf_location_rules_match_page_level'], 10, 3);
	}
	
	//Register the rule type for Page Level
	public function acf_location_rules_types( $choices ) {
		$choices['Page']['page_level'] = 'Page Level';
		return $choices;
	}
	
	//Fill the choices with dropdown values
	public function acf_location_rules_values( $choices ) {
		$i = 0;
		for($i = 0; $i <= 10; $i++){
			$choices[$i] = $i;
		}
		return $choices;
	}

	//Check and match the value with the current page level
	public function acf_location_rules_match_page_level( $match, $rule, $options ){
		$id = !empty($_GET['post'])? $_GET['post']: 0;
		$id = preg_replace('/\D/', '', $id);
		if( $id ){
			$level = ($rule['param'] == 'page_level')? $rule['value']: null;
			if( count(get_post_ancestors($id)) == $level ){
				$match = true;
			}
			return $match;
		}
	}
	
	//Register the page ancestor
	public function acf_location_rules_page_ancestor($choices) {
		$choices['Page']['page_ancestor'] = 'Page Ancestor';
		return $choices;
	}
	
	//Make a dropdown for the ancestor pages only
	public function acf_location_rules_values_page_ancestor($choices){
		$pages = get_pages( array('sort_column' => 'menu_order', 'sort_order' => 'asc', 'parent' => 0));
		$choices['0'] = 'None';
		foreach( $pages as $page ){
			$choices[$page->ID] = $page->post_title;
		}
		return $choices;
	}
	
	//Check and match if the page ancestor matches the value
	public function acf_location_rules_match_page_ancestor($match, $rule, $options) {
		$ancestor = get_ancestors($_GET['post'], 'page');
		$ancestor = array_reverse($ancestor);
		$ancestor = array_filter($ancestor);
		$ancestor = (!empty($ancestor[0]))? $ancestor[0]: null;
		if( $ancestor ){
			if( !empty($rule['param']) and $rule['param'] == 'page_ancestor' and !empty($rule['value']) and $rule['value'] == $ancestor ){
				$match = true;
			}
		}
		return $match;
	}
	
}

new acf_page_level;
