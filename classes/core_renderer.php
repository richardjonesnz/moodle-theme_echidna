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

defined('MOODLE_INTERNAL') || die();

class theme_echidna_core_renderer extends \theme_boost\output\core_renderer {
    // Add your methods here.

    public function echidnablocks($region, $blocksperrow = 0) {

        // If param blocksperrow is zero horizontal blocks are not required.

        $output = '';
        $displayregion = $this->page->apply_theme_region_manipulations($region);
        $editing = $this->page->user_is_editing();

        if (($this->page->blocks->region_has_content($displayregion, $this)) || ($editing)) {
            $attributes = array(
                'id' => 'block-region-'.$region,
                'class' => 'block-region',
                'data-blockregion' => $region,
                'data-droptarget' => '1'
            );

            $output .= html_writer::start_tag('section', array('class' => 'd-print-none'));
            $regioncontent = '';

            if ($editing) {
                if ($blocksperrow > 0) {
                    $attributes['class'] .= ' colly-container editing';
                }
                $output .= html_writer::start_tag('div', array('class' => 'row'));
                $output .= html_writer::tag('span', html_writer::tag('span', get_string('region-'.$region, 'theme_echidna')),
                    array('class' => 'regionname col-12 text-center'));
                $output .= html_writer::end_tag('div');
            }

            if ($this->page->blocks->region_has_content($region, $this)) {
                if ($blocksperrow > 0) {
                    // Call the core renderer if horizontal blocks not set.
                    $regioncontent .= $this->echidna_blocks_for_region($region, $blocksperrow, $editing);
                } else {
                    $regioncontent .= $this->blocks_for_region($region);
                }
            }

            $output .= html_writer::tag('aside', $regioncontent, $attributes);
            $output .= html_writer::end_tag('section');
        }

        return $output;
    }

    protected function echidna_blocks_for_region($region, $blocksperrow, $editing) {

        $blockcontents = $this->page->blocks->get_content_for_region($region, $this);
        $output = '';

        $blockcount = count($blockcontents);

        // Check there are blocks.
        if ($blockcount >= 1) {
            if (!$editing) {
                $output .= html_writer::start_tag('div', array('class' => 'colly-container'));
            }

            $lastblock = null;
            $zones = array();
            foreach ($blockcontents as $bc) {
                if ($bc instanceof block_contents) { // MDL-64818.
                    $zones[] = $bc->title;
                }
            }

            // When editing we want all the blocks to be the same for ease of editing.
            if (($blocksperrow > 4) || ($editing)) {
                $blocksperrow = 4; // Will result in a 'colly-4' when more than one row.
            }

            $rows = $blockcount / $blocksperrow; // Maximum blocks per row.

            if (!$editing) {
                if ($rows <= 1) {
                    $colly = $blockcount;
                    if ($colly < 1) {

                        // Should not happen but a fail safe.  Will look intentionally odd.
                        $colly = 4;
                    }
                } else {
                    $colly = $blocksperrow;
                }
            }

            $currentblockcount = 0;
            $currentrow = 0;
            $currentrequiredrow = 1;

            foreach ($blockcontents as $bc) {

                if (!$editing) { // Fix to four columns only when editing - done in CSS.
                    $currentblockcount++;
                    if ($currentblockcount > ($currentrequiredrow * $blocksperrow)) {
                        // Tripping point.
                        $currentrequiredrow++;
                        // Break...
                        $output .= html_writer::end_tag('div');
                        $output .= html_writer::start_tag('div', array('class' => 'colly-container'));
                        // Recalculate colly if needed...
                        $remainingblocks = $blockcount - ($currentblockcount - 1);
                        if ($remainingblocks < $blocksperrow) {
                            $colly = $remainingblocks;
                            if ($colly < 1) {
                                // Should not happen but a fail safe.  Will look intentionally odd.
                                $colly = 4;
                            }
                        }
                    }

                    if ($currentrow < $currentrequiredrow) {
                        $currentrow = $currentrequiredrow;
                    }

                    $bc->attributes['class'] .= ' colly-'.$colly;
                }

                if ($bc instanceof block_contents) {
                    $output .= $this->block($bc, $region);
                    $lastblock = $bc->title;
                } else if ($bc instanceof block_move_target) {
                    $output .= $this->block_move_target($bc, $zones, $lastblock, $region);
                } else {
                    throw new coding_exception('Unexpected type of thing ('.get_class($bc).') found in list of block contents.');
                }
            }
            if (!$editing) {
                $output .= html_writer::end_tag('div');
            }
        }

        return $output;
    }
}
