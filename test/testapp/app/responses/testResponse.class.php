<?php

/**
* @package
* @subpackage
* @author      Jouanneau Laurent
* @contributor
* @copyright   2021 Jouanneau laurent
* @licence     GNU General Public Licence see LICENCE file or http://www.gnu.org/licenses/gpl.html
*/

require_once (JELIX_LIB_PATH.'core/response/jResponseHtml.class.php');

class testResponse extends jResponseHtml {


   public $bodyTpl = 'test~main';

   // modifications communes aux actions utilisant cette reponses
   protected function doAfterActions(){
       $this->title .= ($this->title !=''?' - ':'').' Test Resque';

       $this->body->assignIfNone('MAIN','<p>Empty page</p>');
       $this->body->assignIfNone('page_title','Application Test for Resque module');
       //$this->addCSSLink(jApp::config()->urlBasePath().'styles.css');
   }
}
