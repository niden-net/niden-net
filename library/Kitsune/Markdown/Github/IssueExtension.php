<?php

namespace Kitsune\Markdown\Github;

use Ciconia\Common\Text;
use Ciconia\Extension\ExtensionInterface;
use Ciconia\Markdown;
use Ciconia\Exception;

/**
 * Converts @[GI:9999] to a github issue link
 */
class IssueExtension implements ExtensionInterface
{
    private $accountName = '';
    private $issueUrl    = '[#%s](https://github.com/%s/%s/issues/%s)';
    private $projectName = '';

    public function setAccountName($accountName)
    {
        $this->accountName = $accountName;

        return $this;
    }

    public function setProjectName($projectName)
    {
        $this->projectName = $projectName;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function register(Markdown $markdown)
    {
        $this->markdown = $markdown;

        $markdown->on('inline', array($this, 'processIssues'));
    }

    /**
     * @param Text $text
     *
     * @throws \Exception
     */
    public function processIssues(Text $text)
    {
        if (true === empty($this->accountName) || true === empty($this->projectName)) {
            throw new \Exception('Github account name or project are not set');
        }

        /**
         * Turn the token to a github issue URL
         */
        $text->replace(
            '(\[GI:(\d+)\])',
            function (Text $w, Text $issue) {
                return sprintf(
                    $this->issueUrl,
                    $issue,
                    $this->projectId,
                    $issue
                );
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'githubissue';
    }
}
