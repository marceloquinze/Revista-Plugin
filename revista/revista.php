<?php

/**
 * Plugin Name: Revista
 * Plugin URI: https://www.wordpress.org/mv-slider
 * Description: This plugins automatically creates featured images to a specific client website
 * Version: 1.0
 * Requires at least: 5.6
 * Author: Marcelo Vieira
 * Author URI: https://www.codigowp.net
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: revista
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Revista')) {
    class Revista
    {

        private static $instance;

        public static function get_instance()
        {
            if (null === self::$instance) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        private function __construct()
        {
            // Chama o método que define as constantes
            $this->define_constants();

            // Chama o arquivo que processa o CSV 
            require_once(REVISTA_INCLUDES . '/ProcessCSV.php');

            // // Chama o método que registra a tabela de metadados no banco de dados
            require_once(REVISTA_INCLUDES . '/RevistaTable.php');

            // Chama o método que adiciona o menu
            add_action('admin_menu', array($this, 'add_menu_page'));
        }

        public function define_constants()
        {
            define('REVISTA_VERSION', '1.0');
            define('REVISTA_FILE', __FILE__);
            define('REVISTA_PATH', dirname(REVISTA_FILE));
            define('REVISTA_INCLUDES', REVISTA_PATH . '/includes');
            define('REVISTA_URL', plugins_url('', REVISTA_FILE));
            define('REVISTA_ASSETS', REVISTA_URL . '/assets');
        }

        public function add_menu_page()
        {
            add_menu_page(
                'Revista',
                'Revista',
                'manage_options',
                'revista',
                array($this, 'revista_page'),
                'dashicons-format-image',
                20
            );
        }

        public function revista_page()
        {
            // Chama a view com o formulário de upload
            // Chama o método que processa o arquivo         
            new ProcessCSV();
            require_once(REVISTA_PATH . '/views/revista-view.php');
        }

        public static function activate()
        {
            // Chama o método que registra a tabela de metadados no banco de dados
            new RevistaTable();
        }
    }
}
if (class_exists('Revista')) {

    register_activation_hook(__FILE__, array('Revista', 'activate'));

    $revista = Revista::get_instance();
}
