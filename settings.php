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

    $defaults = [
        'Arial',
        'Verdana',
        'Tahoma',
        'Trebuchet MS',
    ];

    $settings->add(
        new admin_setting_configtextarea($plugin . '/fonts',
                new lang_string('fonts', $plugin),
                new lang_string('fonts_desc', $plugin),
                implode("\r\n", $defaults), PARAM_TEXT, 80, 10));

    // Licensing settings
    $settings->add(new admin_setting_heading(
        $plugin . '/licensingheading',
        new lang_string('licensingheading', $plugin),
        ''
    ));

    $settings->add(new admin_setting_configtext(
        $plugin . '/license_key',
        new lang_string('license_key', $plugin),
        new lang_string('license_key_desc', $plugin),
        '',
        PARAM_RAW_TRIMMED
    ));

    // License validation information (read-only)
    $validationData = get_config($plugin, 'license_validation_data');
    $lastChecked = get_config($plugin, 'license_last_checked');
    $validationError = get_config($plugin, 'license_validation_error');

    $infoText = '';
    if ($validationError) {
        $infoText = get_string('validation_error', $plugin) . ': ' . $validationError;
    } elseif ($validationData) {
        $data = json_decode($validationData, true);
        $status = $data['status'] ?? 'unknown';
        $valid = $data['valid'] ?? false;
        $expiresAt = $data['expires_at'] ?? null;

        $infoText = get_string('license_status', $plugin) . ': ' . $status . "<br>";
        $infoText .= get_string('license_valid', $plugin) . ': ' . ($valid ? get_string('yes') : get_string('no')) . "<br>";
        if ($expiresAt) {
            $infoText .= get_string('license_expires', $plugin) . ': ' . date('Y-m-d', $expiresAt) . "<br>";
        }
    }

    if ($lastChecked) {
        $infoText .= get_string('last_validated', $plugin) . ': ' . date('Y-m-d H:i:s', $lastChecked);
    }

    if (trim($infoText)) {
        $settings->add(new admin_setting_heading(
            $plugin . '/validation_info',
            new lang_string('license_validation_info', $plugin),
            $infoText
        ));
    }
}

// Add the settings page under the plugin category so it doesn't create
// a second top-level settings entry with the same name.
$ADMIN->add($plugin, $settings);
