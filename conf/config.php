<?php

//change DB details
$db_host = 'localhost';
$db_user = 'root';
$db_psw = 'R5UKBt0pNw';

$WPes_root_path = '../WPs/'; //you should set the complete path (from the root of your server)
$wp_base_domain = 'http://127.0.0.1/'; //remember del slash
$wp_base_url_no_domain = 'local-project/WPes/'; //the container folder
$wp_base_url = $wp_base_domain.'/'.$wp_base_url_no_domain;
$wp_config_tmpl_filename = '_template-hsdk/wp-config-template.php';
$WP_db_template = '_template-hsdk/WP_db_template.sql';;
$htaccess_tmpl_filename = '_template-hsdk/.htaccess-template';;
