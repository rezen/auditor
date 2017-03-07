<?php
/**
 * @package Auditor
 * @author Andres Hermosilla <andres@ahermosilla.com>
 * @license GPL-2.0+
 * @link http://ahermosilla.com
 * @copyright
 *
 * @wordpress-plugin
 * Plugin Name: Auditor
 * Plugin URI: http://ahermosilla.com
 * Description: Developer plugin to leverage to audit events on your WordPress platform
 * Version: 1.0.0
 * Author: Andres Hermosilla
 * Text Domain: auditor
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

function activate_auditor()
{
    require_once 'Auditor/MigrationInstall.php';
    global $wpdb;
    $migration = new Auditor\MigrationInstall($wpdb);
    $migration->up();
}

function run_auditor()
{
    require_once 'Auditor/Activity.php';
    require_once 'Auditor/Interactor.php';
    require_once 'Auditor/ActivityScout.php';
    require_once 'Auditor/ActivityRecorder.php';
    require_once 'Auditor/AuditRouter.php';
    require_once 'Auditor/AuditorIntegration.php';

    $auditor = Auditor\AuditorIntegration::create(dirname(__FILE__) . '/views');
    $auditor->hooks();
}

register_activation_hook(__FILE__, 'activate_auditor');
run_auditor();
