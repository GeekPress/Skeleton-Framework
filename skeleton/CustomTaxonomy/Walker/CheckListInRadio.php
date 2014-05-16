<?php

/*
 * This file is part of the Skeleton package.
 *
 * (c) Jonathan Buttigieg <contact@wp-media.me>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Skeleton\CustomTaxonomy\Walker;

class CheckListInRadio extends \Walker {
  
  var $db_fields = array( 'parent'=>'parent', 'id'=>'term_id' );

  function start_lvl( &$output, $depth = 0, $args = array() ) {
    $indent = str_repeat("\t", $depth);
    $output .= "$indent<ul class='children'>\n";
  }

  function end_lvl( &$output, $depth = 0, $args = array() ) {
    $indent = str_repeat("\t", $depth);
    $output .= "$indent</ul>\n";
  }

  function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 )
  {
    static $n=0; $n++;
    extract( $args );
    $output .= "\n".'<li id="' . $taxonomy . '-' . $category->term_id . '">' .
              '<label class="selectit">' .
                '<input value="' . $category->term_id . '" type="radio" name="tax_input[' . $taxonomy . '][]" id="in-'.$taxonomy.'-'.$category->term_id . '"' . checked( in_array( $category->term_id, $selected_cats ) || $n==1, true, false ) . ' /> ' .
                  esc_html( apply_filters('the_category', $category->name )) .
              '</label>' .
            '</li>';
  }
  
}