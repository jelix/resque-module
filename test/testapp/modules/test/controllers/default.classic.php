<?php
/**
 * @author   Laurent Jouanneau
 * @copyright 2021 Laurent Jouanneau
 * @link     http://jelix.org
 * @licence MIT
 */

class defaultCtrl extends jController
{
    /**
     *
     */
    function index()
    {
        $rep = $this->getResponse('html');
        $tpl = new jTpl();


        $rep->body->assign('page_title', 'Home');
        $rep->body->assign('MAIN', $tpl->fetch('index'));
        return $rep;
    }

}

