<?php

namespace Kitsune;

use Ciconia\Ciconia;
use Ciconia\Extension\Gfm\FencedCodeBlockExtension;
use Ciconia\Extension\Gfm\TaskListExtension;
use Ciconia\Extension\Gfm\InlineStyleExtension;
use Ciconia\Extension\Gfm\WhiteSpaceExtension;
use Ciconia\Extension\Gfm\TableExtension;
use Ciconia\Extension\Gfm\UrlAutoLinkExtension;

use Kitsune\Markdown\Github\MentionExtension;
use Kitsune\Markdown\Github\IssueExtension;
use Kitsune\Markdown\Github\PullRequestExtension;

class Markdown extends Ciconia
{
    public function __construct(\Ciconia\Renderer\RendererInterface $renderer = null)
    {
        parent::__construct($renderer);

        $this->addExtension(new FencedCodeBlockExtension());
        $this->addExtension(new TaskListExtension());
        $this->addExtension(new InlineStyleExtension());
        $this->addExtension(new WhiteSpaceExtension());
        $this->addExtension(new TableExtension());
        $this->addExtension(new UrlAutoLinkExtension());
        $this->addExtension(new MentionExtension());

        $extension = new IssueExtension();
        $extension->setIssueUrl(
            '[#%s](https://github.com/phalcon/cphalcon/issues/%s)'
        );
        $this->addExtension($extension);

        $extension = new PullRequestExtension();
        $extension->setIssueUrl(
            '[#%s](https://github.com/phalcon/cphalcon/pull/%s)'
        );
        $this->addExtension($extension);

    }
}

