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
 * Tiny Font family plugin plugin for Moodle.
 *
 * @package     tiny_fontfamily
 * @copyright   2024 Mikko Haiku <mikko.haiku@mediamaisteri.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tiny_fontfamily;

use context;
use editor_tiny\plugin;
use editor_tiny\plugin_with_buttons;
use editor_tiny\plugin_with_menuitems;
use editor_tiny\plugin_with_configuration;

/**
 * Plugininfo class.
 */
class plugininfo extends plugin implements plugin_with_configuration, plugin_with_buttons, plugin_with_menuitems {

    /**
     * Default font families, used whenever the license hasn't been validated.
     */
    private const DEFAULT_FONTS = ['Arial', 'Verdana', 'Tahoma', 'Trebuchet MS'];

    /**
     * Get available buttons.
     *
     * @return array
     */
    public static function get_available_buttons(): array {
        return [
            'tiny_fontfamily/plugin',
        ];
    }

    /**
     * Get available menuitems.
     *
     * @return array
     */
    public static function get_available_menuitems(): array {
        return [
            'tiny_fontfamily/plugin',
        ];
    }

    /**
     * Get plugin configuration.
     *
     * Until the license has been validated successfully, the configured font
     * list is ignored and the plugin behaves as if it was never customised,
     * rather than exposing a previously saved configuration.
     *
     * @return array
     */
    public static function get_plugin_configuration_for_context(
        context $context,
        array $options,
        array $fpoptions,
        ?\editor_tiny\editor $editor = null
    ): array {
        if (!self::is_license_valid()) {
            return ['fonts' => self::DEFAULT_FONTS];
        }

        $rawfonts = get_config('tiny_fontfamily', 'fonts');
        if ($rawfonts === false || trim($rawfonts) === '') {
            $rawfonts = implode("\r\n", self::DEFAULT_FONTS);
        }

        return ['fonts' => preg_split('/\r\n|\r|\n/', $rawfonts)];
    }

    /**
     * Whether the plugin's license has been validated successfully.
     *
     * @return bool
     */
    private static function is_license_valid(): bool {
        $error = get_config('tiny_fontfamily', 'license_validation_error');
        $data = get_config('tiny_fontfamily', 'license_validation_data');

        if (!empty($error) || empty($data)) {
            return false;
        }

        $decoded = json_decode($data, true);
        return !empty($decoded['valid']);
    }
}
