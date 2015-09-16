<?php

namespace Kitsune;

use Phalcon\Mvc\Controller as PhController;

class Controller extends PhController
{
    public function initialize()
    {
        $template = 'main';
        if (true === boolval($this->config->blog->customLayout)) {
            $template = 'custom';
        }
        $this->view->setTemplateAfter($template);
        $this->view->setVar('cdnUrl', $this->config->cdnUrl);
        $this->view->setVar('tagCloud', $this->finder->getTagCloud());
        $this->view->setVar('menuList', $this->finder->getList());
    }
}
