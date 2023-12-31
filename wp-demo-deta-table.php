<?php
/*
 * Plugin Name:       Wp Demo Data table
 * Plugin URI:        https://example.com/plugins/the-basics/
 * Description:       Handle the basics with this plugin.
 * Version:           1.10.3
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Rezwanul Haque
 * Author URI:        https://author.example.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://example.com/my-plugin/
 * Text Domain:       wddt
 * Domain Path:       /languages
 */
// if (!defined('ABSPATH')) {
//    exit;
// }
// ##########
//?  table lists
// ##########
require_once('data-persons-table.php');

//? load text domain
function wddt_plugin_translation()
{
   load_plugin_textdomain('your-plugin-textdomain', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}

add_action('plugins_loaded', 'wddt_plugin_translation');

// ##########
//?add detatable admin page
// ##########

add_action("admin_menu", "datable_admin_page");

function datable_admin_page()
{
   add_menu_page(
      __('Data Table', 'tabledate'),
      __('Data Table', 'tabledate'),
      'manage_options',
      'datatable',
      'datable_display_table'
   );
}
function datable_display_gender($item)
{
   $sex = $_REQUEST['filter_s'] ?? 'all';
   if ('all' == $sex) {
      return true;
   } else {
      if ($sex == $item['sex']) {
         return true;
      }
   }
   return false;
}
function datable_search_by_name($item)
{
   $name = strtolower($item['name']);
   $search_name = sanitize_text_field($_REQUEST['s']);
   if (strpos($name, $search_name) !== false) {
      return true;
   }
   return false;
}

function datable_display_table()
{
   // ##########
   //? adding demo data link
   // ##########
   include_once('demo-database/dataset.php');


   // ##########
   //? age sorting
   // ##########

   $orderby = $_REQUEST['orderby'] ?? '';
   $order = $_REQUEST['order'] ?? '';
   // ##########
   //? add table functionality
   // ##########
   $table = new Persons_Table();
   if ('age' == $orderby) {
      if ('asc' === $order) {
         usort($data, function ($item1, $item2) {
            return $item2['age'] <=> $item1['age'];
         });
      } else {
         usort($data, function ($item1, $item2) {
            return $item1['age'] <=> $item2['age'];
         });
      }
   } else if ('name' == $orderby) {
      if ('asc' === $order) {
         usort($data, function ($item1, $item2) {
            return $item2['name'] <=> $item1['name'];
         });
      } else {
         usort($data, function ($item1, $item2) {
            return $item1['name'] <=> $item2['name'];
         });
      }
   }
   if (isset($_REQUEST['s']) && !empty($_REQUEST['s'])) {
      $search_name = $_REQUEST['s'];
      $data = array_filter($data, 'datable_search_by_name');
   }
   if (isset($_REQUEST['filter_s']) && !empty($_REQUEST['filter_s'])) {
      $search_name = $_REQUEST['s'];
      $data = array_filter($data, 'datable_display_gender');
   }
   $table->set_data($data);
   $table->prepare_items();
?>
   <div class="warp">
      <h2><?php _e('Persons', 'wddt'); ?></h2>
      <form method="GET">
         <?php
         $table->search_box('search', 'search_id');
         $table->display();
         ?>
         <input type="hidden" name="page" value="<?php echo $_REQUEST['page']; ?>">
      </form>
   </div>

<?php

}
