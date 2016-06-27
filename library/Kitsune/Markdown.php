<?php

namespace Kitsune;

use Ciconia\Ciconia;
use Ciconia\Extension\Gfm\FencedCodeBlockExtension;
use Ciconia\Extension\Gfm\TaskListExtension;
use Ciconia\Extension\Gfm\InlineStyleExtension;
use Ciconia\Extension\Gfm\WhiteSpaceExtension;
use Ciconia\Extension\Gfm\TableExtension;
use Ciconia\Extension\Gfm\UrlAutoLinkExtension;
use Ciconia\Renderer\RendererInterface;

use Kitsune\Markdown\Github\MentionExtension;
use Kitsune\Markdown\Github\IssueExtension;
use Kitsune\Markdown\Github\PullRequestExtension;

class Markdown extends Ciconia
{
    public function __construct(RendererInterface $renderer = null)
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
        $extension
            ->setAccountName('phalcon')
            ->setProjectName('cphalcon');
        $this->addExtension($extension);

        $extension = new PullRequestExtension();
        $extension
            ->setAccountName('phalcon')
            ->setProjectName('cphalcon');
        $this->addExtension($extension);
    }
}

