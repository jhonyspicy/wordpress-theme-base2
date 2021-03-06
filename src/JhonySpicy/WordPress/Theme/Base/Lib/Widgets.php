<?php
namespace Jhonyspicy\Wordpress\Theme\Base\Lib;

abstract class Widgets {
	static $widgetList = array();

	/**
	 * 自分自身の管理画面なのかどうか
	 * @return bool
	 */
	static public function is_self() {
		$screen = get_current_screen();

		if ($screen->id != self::name()) {
			return false;
		}

		return true;
	}

	/**
	 * クラス名を取得する(名前空間込)
	 *
	 * @return string
	 */
	static public function class_name() {
		return get_called_class();
	}

	/**
	 * 投稿タイプの名前(ID?)を取得する
	 *
	 * @param null $name
	 *
	 * @return string
	 */
	static public function name($name = null) {
		if (!$name) {
			$name = self::class_name();
		}
		$v = explode('\\', $name);

		return strtolower(trim(preg_replace('/([A-Z])/', '-$1', end($v)), '-'));

//		return strtolower(end($v));
	}

	/**
	 * 実際にウィジェットを登録する
	 */
	static public function register_widget($widget) {
		self::$widgetList[] = $widget;

		register_widget($widget);
	}

	static public function add_hooks() {
		add_action('admin_print_scripts', array(__CLASS__, 'admin_print_scripts'));
		add_action('admin_print_styles', array(__CLASS__, 'admin_print_styles'));
	}

	static public function admin_print_scripts() {
		wp_enqueue_media(); //これがないとjavascriptで「wp.media()」実行時にエラーとなる。詳細は不明

		foreach(self::$widgetList as $widget) {
			$widgetName = self::name($widget);

			if (method_exists($widget, 'admin_print_scripts')) {
				call_user_func(array($widget, 'admin_print_scripts'));
			} else {
				$file_path = '/scripts/admin/widgets/'. $widgetName .'.js';

				if (is_file(get_template_directory() . $file_path)) {
					wp_enqueue_script($widgetName . '_script', get_template_directory_uri() . $file_path, array('jquery'), '1.0.0', true);
				}
			}
		}
	}

	static public function admin_print_styles() {
		foreach(self::$widgetList as $widget) {
			$widgetName = self::name($widget);

			if (method_exists($widget, 'admin_print_styles')) {
				call_user_func(array($widget, 'admin_print_styles'));
			} else {
				$file_path = '/styles/admin/widgets/'. $widgetName .'/style.css';

				if (is_file(get_template_directory() . $file_path)) {
					wp_enqueue_style($widgetName, get_template_directory_uri() . $file_path, array(), '1.0.0');
				}
			}
		}
	}
}