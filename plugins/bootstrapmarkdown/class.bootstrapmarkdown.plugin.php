<?php if (!defined('APPLICATION')) exit;

$PluginInfo['bootstrapmarkdown'] = array(
  'Name'        => "Bootstrap Markdown",
  'Description' => "Editor plugin for Vanilla using the Bootstrap Markdown jQuery plugin. For use with Bootstrap for Vanilla and other Bootstrap-based themes.",
  'Version'     => '1.0.0',
  'PluginUrl'   => 'https://github.com/kasperisager/vanilla-bootstrapmarkdown',
  'Author'      => "Kasper Kronborg Isager",
  'AuthorEmail' => 'kasperisager@gmail.com',
  'AuthorUrl'   => 'https://github.com/kasperisager',
  'License'     => 'MIT'
);

/**
 * Bootstrap Markdown Plugin
 *
 * @author    Kasper Kronborg Isager <kasperisager@gmail.com>
 * @copyright 2014 (c) Kasper Kronborg Isager
 * @license   MIT
 * @since     1.0.0
 */
class BootstrapMarkdownPlugin extends Gdn_Plugin {
  /**
   * Initialize Bootstrap Markdown
   *
   * @since  1.0.0
   * @access public
   * @param  Gdn_Form $sender
   */
  public function Gdn_Form_beforeBodyBox_handler($sender) {
    // Make sure that Markdown is used
    $sender->setValue('Format', 'Markdown');

    // Remove jQuery Autogrow as it interferes with the editor
    Gdn::controller()->removeJsFile('jquery.autogrow.js');

    // Add the assets we need for the editor
    Gdn::controller()->addCssFile($this->getResource('design/editor.css', false, false));
    Gdn::controller()->addJsFile($this->getResource('js/editor.js', false, false));
  }
}
