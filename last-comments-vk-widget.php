<?php
/*
	Plugin Name: Last comments VK Widget
	Description: Widget last comments VK
	Version: 1.3
	Author: Somonator
	Author URI: mailto:somonator@gmail.com
	Text Domain: lcw
	Domain Path: /lang
*/

/*  
	Copyright 2016  Alexsandr (email: somonator@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class lcw_widget extends WP_Widget {
	function __construct() {
		parent::__construct('', __('Last comments VK', 'lcw'), array(
			'description' => __('Widget last comments VK', 'lcw')
		));
	}
	
	public function widget( $args, $instance ) {
		$title = apply_filters('widget_title', $instance['title']);
		$appid = @ $instance['appid']; 	
		$limit = @ $instance['limit'];
		
		echo $args['before_widget'];
		
		if (!empty($title)) {
		  echo $args['before_title'] . $title . $args['after_title'];
		}
		
		if (!empty($appid)) {
			echo '<div id="container-' . $this->id . '"></div>';
			echo '<script>window.onload = function () {VK.init({apiId: ' . $appid . ', onlyWidgets: false}); VK.Widgets.CommentsBrowse("container-' . $this->id . '", {limit: ' . $limit . ', mini: 0});}</script>';
		} else {
			echo '<p>' . __('Please enter vk app id for work widget.', 'lcw') . '</p>';
		}
		
		echo $args['after_widget'];
	}

	public function form($instance) {
		$title = @ $instance['title'] ? : null;
		$appid = @ $instance['appid'] ? : null;
		$limit = @ $instance['limit'] ? : '5';

		echo $this->get_field_html('title', $title, __('Title:', 'lcw'));
		echo $this->get_field_html('appid', $appid, __('App id VK:*', 'lcw'), true);
		echo $this->get_field_html('limit', $limit, __('Number comments:', 'lcw'));
	}

	public function get_field_html($name, $val, $translate, $required = '') {
		$name = $this->get_field_name($name);
		$val =  esc_attr($val);
		$req = $required ? 'required' : null;
		
		echo '<p>';
			echo '<label>' . $translate;
			echo '<input type="text" name="' . $name . '" value="' . $val . '" class="widefat" ' . $req  . '>';
		echo '</p>';
	}

	public function update($new_instance, $old_instance) {
		$instance['title'] = !empty($new_instance['title']) ? strip_tags($new_instance['title']) : null;
		$instance['appid'] = !empty($new_instance['appid']) ? strip_tags($new_instance['appid']) : null;
		$instance['limit'] = !empty($new_instance['limit']) ? strip_tags($new_instance['limit']) : null;

		return $instance;
	}

} 

class lcw_includes {
	function __construct() {
		if (is_active_widget(false, false, 'lcw_widget') || is_customize_preview()) {
			add_action('wp_enqueue_scripts', array($this, 'vk_api'), 11);
			add_action('wp_footer', array($this, 'add_scripts'));
		}

		add_action('admin_footer', array($this, 'add_scripts_to_admin'));
		add_action('plugins_loaded', array($this, 'lang_load'));		
	}
	
	public function vk_api() {
		if (!wp_script_is('vk-api', 'enqueued')) {
			wp_enqueue_script('vk-api', '//vk.com/js/api/openapi.js');
		}
	}
	
	public function add_scripts() {
		echo '<style>[id*="container-lcw_widget"],[id*="container-lcw_widget"] iframe{width:100%!important;max-width:100%!important;display:table;}</style>';
	}
	
	public function add_scripts_to_admin() {
		if (get_current_screen()->base == 'widgets') {
			echo '<script>jQuery(function($){$(document).delegate(\'[id*="lcw_widget"] input[name="savewidget"]\', \'click\', function() {var $form = $(this).parents().closest(\'form\');$form.find(\'input[required]\').each(function() {if (!$(this)[0].checkValidity()) {$(this)[0].reportValidity();}});});});</script>';
		}
	}
	
	public function lang_load() {
		load_plugin_textdomain('lcw', false, dirname(plugin_basename( __FILE__ )) . '/lang/'); 
	}
}


/**
* Init widget.
*/
function register_lcw_widget() {
	register_widget('lcw_widget');
}

add_action('widgets_init', 'register_lcw_widget');

/**
* Stylea, scripts and lang.
*/
new lcw_includes();	