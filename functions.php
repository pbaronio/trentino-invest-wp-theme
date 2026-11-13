<?php
/**
 * Trentino Invest — bootstrap del tema.
 *
 * @package TrentinoInvest
 */

defined( 'ABSPATH' ) || exit;

define( 'TI_VERSION', '1.0.0' );
define( 'TI_DIR', get_template_directory() );
define( 'TI_URI', get_template_directory_uri() );

require_once TI_DIR . '/inc/defaults.php';
require_once TI_DIR . '/inc/helpers.php';
require_once TI_DIR . '/inc/setup.php';
require_once TI_DIR . '/inc/assets.php';
require_once TI_DIR . '/inc/post-types.php';
require_once TI_DIR . '/inc/fields.php';
require_once TI_DIR . '/inc/admin.php';
require_once TI_DIR . '/inc/svg-upload.php';
require_once TI_DIR . '/inc/importer.php';
