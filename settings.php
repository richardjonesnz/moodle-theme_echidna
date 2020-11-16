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
 * Echidna theme.
 *
 * @package    theme_echidna
 * @copyright  &copy; 2020-onwards G J Barnard.
 * @author     G J Barnard - {@link http://moodle.org/user/profile.php?id=442195}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    // Add your settings here.

    // Drawer widths (after Fordson)
    $name = 'theme_echidna/drawerwidthechidna';
    $title = get_string('drawerwidthechidna', 'theme_echidna');
    $description = get_string('drawerwidthechidna_desc', 'theme_echidna');;
    $default = '280px';
    $choices = array(
            '180px' => '180px',
            '200px' => '200px',
            '220px' => '220px',
            '240px' => '240px',
            '260px' => '260px',
            '280px' => '280px',
            '300px' => '300px',
            '320px' => '320px',
            '340px' => '340px',
        );
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $settings->add($setting);

    // Custom CSS.
    $name = 'theme_echidna/customcss';
    $title = get_string('customcss', 'theme_echidna');
    $description = get_string('customcssdesc', 'theme_echidna');
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);
}
