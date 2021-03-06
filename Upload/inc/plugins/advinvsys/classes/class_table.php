<?php

/**
 * MyBB 1.6
 * Copyright 2010 MyBB Group, All Rights Reserved
 *
 * Website: http://mybb.com
 * License: http://mybb.com/about/license
 *
 * $Id: class_table.php 5297 2010-12-28 22:01:14Z Tomm $
 */

/**
 * Generate a data grid/table.
 */
class AISTable {

    /**
     * @var array Array of cells for the current row.
     */
    private $_cells = array();

    /**
     * @var array Array of rows for the current table.
     */
    private $_rows = array();

    /**
     * @var array Array of headers for the current table.
     */
    private $_headers = array();

    /**
     * Construct an individual cell for this table.
     *
     * @param string The HTML content for this cell.
     * @param array Array of extra information about this cell (class, id, colspan, rowspan, width)
     */
    function construct_cell($data, $extra=array()) {
        $this->_cells[] = array("data" => $data, "extra" => $extra);
    }

    /**
     * Construct a row from the earlier defined constructed cells for the table.
     *
     * @param array Array of extra information about this row (class, id)
     */
    function construct_row($extra = array()) {
        $i = 1;
        // We construct individual cells here
        foreach ($this->_cells as $key => $cell) {
            $cells .= "\t\t\t<td";
            if ($key == 0) {
                $cell['extra']['class'] .= " first";
            } elseif (!$this->_cells[$key + 1]) {
                $cell['extra']['class'] .= " last";
            }
            if ($i == 2) {
                $cell['extra']['class'] .= " alt_col";
                $i = 0;
            }
            $i++;
            if ($cell['extra']['class']) {
                $cells .= " class=\"" . trim($cell['extra']['class']) . "\"";
            }
            if ($cell['extra']['style']) {
                $cells .= " style=\"" . $cell['extra']['style'] . "\"";
            }
            if ($cell['extra']['id']) {
                $cells .= $cell['extra']['id'];
            }
            if (isset($cell['extra']['colspan']) && $cell['extra']['colspan'] > 1) {
                $cells .= " colspan=\"" . $cell['extra']['colspan'] . "\"";
            }
            if (isset($cell['extra']['rowspan']) && $cell['extra']['rowspan'] > 1) {
                $cells .= " rowspan=\"" . $cell['extra']['rowspan'] . "\"";
            }
            if ($cell['extra']['width']) {
                $cells .= " width=\"" . $cell['extra']['width'] . "\"";
            }
            $cells .= ">";
            $cells .= $cell['data'];
            $cells .= "</td>\n";
        }
        $data['cells'] = $cells;
        $data['extra'] = $extra;
        $this->_rows[] = $data;

        $this->_cells = array();
    }

    /**
     * return the cells of a row for the table based row.
     *
     * @param string The id of the row you want to give it.
     * @param boolean Whether or not to return or echo the resultant contents.
     * @return string The output of the row cells (optional).
     */
    function output_row_cells($row_id, $return=false) {
        $row = $this->_rows[$row_id]['cells'];

        if (!$return) {
            echo $row;
        } else {
            return $row;
        }
    }

    /**
     * Count the number of rows in the table. Useful for displaying a 'no rows' message.
     *
     * @return int The number of rows in the table.
     */
    function num_rows() {
        return count($this->_rows);
    }

    /**
     * Construct a header cell for this table.
     *
     * @param string The HTML content for this header cell.
     * @param array Array of extra information for this header cell (class, style, colspan, width)
     */
    function construct_header($data, $extra=array()) {
        $this->_headers[] = array("data" => $data, "extra" => $extra);
    }

    /**
     * Fetch the built HTML for this table.
     *
     * @param string The heading for this table.
     * @param int The border width for this table.
     * @param string The class for this table.
     * @param string The id for this table.
     * @return string The built HTML.
     */
    function construct_html($heading = '', $border = 1, $class = 'general', $table_id = '') {
        if ($border == 1) {
            $table .= "<div class=\"border_wrapper\">\n";
            if ($heading != "") {
                $table .= "	<div class=\"title\">" . $heading . "</div>\n";
            }
        }
        $table .= "<table";
        $table .= " class=\"" . $class . "\"";
        if ($table_id != '') {
            $table .= " id=\"" . $table_id . "\"";
        }
        $table .= " cellspacing=\"0\">\n";
        if ($this->_headers) {
            $table .= "\t<thead>\n";
            $table .= "\t\t<tr>\n";
            foreach ($this->_headers as $key => $data) {
                $table .= "\t\t\t<th";
                if ($key == 0) {
                    $data['extra']['class'] .= " first";
                } elseif (!$this->_headers[$key + 1]) {
                    $data['extra']['class'] .= " last";
                }
                if ($data['extra']['class']) {
                    $table .= " class=\"" . $data['extra']['class'] . "\"";
                }
                if ($data['extra']['style']) {
                    $table .= " style=\"" . $data['extra']['style'] . "\"";
                }
                if ($data['extra']['width']) {
                    $table .= " width=\"" . $data['extra']['width'] . "\"";
                }
                if (isset($data['extra']['colspan']) && $data['extra']['colspan'] > 1) {
                    $table .= " colspan=\"" . $data['extra']['colspan'] . "\"";
                }
                $table .= ">" . $data['data'] . "</th>\n";
            }
            $table .= "\t\t</tr>\n";
            $table .= "\t</thead>\n";
        }
        $table .= "\t<tbody>\n";
        $i = 1;
        foreach ($this->_rows as $key => $table_row) {
            $table .= "\t\t<tr";
            if ($table_row['extra']['id']) {
                $table .= " id=\"{$table_row['extra']['id']}\"";
            }
            if ($key == 0) {
                $table_row['extra']['class'] .= " first";
            } else if (!$this->_rows[$key + 1]) {
                $table_row['extra']['class'] .= " last";
            }
            if ($i == 2 && !isset($table_row['extra']['no_alt_row'])) {
                $table_row['extra']['class'] .= " alt_row";
                $i = 0;
            }
            $i++;
            if ($table_row['extra']['class']) {
                $table .= " class=\"" . trim($table_row['extra']['class']) . "\"";
            }
            $table .= ">\n";
            $table .= $table_row['cells'];
            $table .= "\t\t</tr>\n";
        }
        $table .= "\t</tbody>\n";
        $table .= "</table>\n";
        // Clean up
        $this->_cells = $this->_rows = $this->_headers = array();
        if ($border == 1) {
            $table .= "</div>";
        }
        return $table;
    }

}

?>
