<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Settings that allow configuring various tiny Font Family plugin features.
 *
 * @package     tiny_fontfamily
 * @copyright   2024 Mikko Haiku <mikko.haiku@mediamaisteri.com
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin = "tiny_fontfamily";

$settings = new admin_settingpage('tiny_fontfamily_settings', new lang_string('settings', $plugin));
if ($ADMIN->fulltree) {
    // Lock every setting except the license key itself until the license has been
    // validated successfully. This uses the same mechanism as forcing a setting via
    // config.php, so the field is both rendered disabled and pinned to its current
    // value even if a request bypassing the disabled control tried to change it.
    $licensevalidationdata = get_config($plugin, 'license_validation_data');
    $licensevalidationerror = get_config($plugin, 'license_validation_error');
    $licensevalid = false;
    if (empty($licensevalidationerror) && !empty($licensevalidationdata)) {
        $decodedlicensedata = json_decode($licensevalidationdata, true);
        $licensevalid = !empty($decodedlicensedata['valid']);
    }

    if (!$licensevalid) {
        $CFG->forced_plugin_settings[$plugin]['fonts'] = get_config($plugin, 'fonts');
    }

    $defaults = [
        'Arial',
        'Verdana',
        'Tahoma',
        'Trebuchet MS',
    ];

    $settings->add(new admin_setting_configtextarea(
        $plugin . '/fonts',
        new lang_string('fonts', $plugin),
        new lang_string('fonts_desc', $plugin),
        implode("\r\n", $defaults),
        PARAM_TEXT,
        80,
        10
    ));

    // Licensing settings.
    $marketplaceurl = 'https://marketplace.moodle.com/plugins/37';
    $settings->add(new admin_setting_heading(
        $plugin . '/licensingheading',
        new lang_string('licensingheading', $plugin),
        html_writer::div(get_string('license_purchase_info', $plugin, $marketplaceurl), 'alert alert-info')
    ));

    $licensekeysetting = new admin_setting_configtext(
        $plugin . '/license_key',
        new lang_string('license_key', $plugin),
        new lang_string('license_key_desc', $plugin),
        '',
        PARAM_RAW_TRIMMED
    );
    // Validate immediately whenever the license key is changed and saved, rather
    // than waiting for the next scheduled run.
    $licensekeysetting->set_updatedcallback(static function () {
        core_php_time_limit::raise(60);
        ob_start();
        (new \tiny_fontfamily\task\validate_license())->execute();
        ob_end_clean();
    });
    $settings->add($licensekeysetting);

    // License validation information (read-only).
    $validationdata = get_config($plugin, 'license_validation_data');
    $lastchecked = get_config($plugin, 'license_last_checked');
    $validationerror = get_config($plugin, 'license_validation_error');

    $infotext = '';
    if ($validationerror) {
        $infotext = get_string('validation_error', $plugin) . ': ' . $validationerror;
    } else if ($validationdata) {
        $data = json_decode($validationdata, true);
        $status = $data['status'] ?? 'unknown';
        $valid = $data['valid'] ?? false;
        $expiresat = $data['expires_at'] ?? null;

        $infotext = get_string('license_status', $plugin) . ': ' . $status . "<br>";
        $yesno = $valid ? get_string('yes') : get_string('no');
        $infotext .= get_string('license_valid', $plugin) . ': ' . $yesno . "<br>";
        if ($expiresat) {
            $infotext .= get_string('license_expires', $plugin) . ': ' . date('Y-m-d', $expiresat) . "<br>";
        }
    }

    if ($lastchecked) {
        $infotext .= "<br>" . get_string('last_validated', $plugin) . ': ' . date('Y-m-d H:i:s', $lastchecked);
    }

    if (trim($infotext)) {
        $settings->add(new admin_setting_heading(
            $plugin . '/validation_info',
            new lang_string('license_validation_info', $plugin),
            $infotext
        ));
    }
}

// Add the settings page under the plugin category so it doesn't create
// a second top-level settings entry with the same name.
$ADMIN->add($plugin, $settings);
