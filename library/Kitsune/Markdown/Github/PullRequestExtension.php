<?php

namespace Kitsune\Markdown\Github;

use Ciconia\Common\Text;
use Ciconia\Exception;
use Ciconia\Extension\ExtensionInterface;
use Ciconia\Markdown;

/**
 * Converts @[GPR:9999] to a github pull request link
 */
class PullRequestExtension implements ExtensionInterface
{
    private $accountName = '';
    private $issueUrl    = '[#%s](https://github.com/%s/%s/pull/%s)';
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

        $markdown->on('inline', array($this, 'processPullRequest'));
    }

    /**
     * @param Text $text
     *
     * @throws \Exception
     */
    public function processPullRequest(Text $text)
    {
        if (true === empty($this->accountName) || true === empty($this->projectName)) {
            throw new \Exception('Github account name or project are not set');
        }

        /**
         * Turn the token to a github issue URL
         */
        $text->replace(
            '(\[GPR:(\d+)\])',
            function (Text $w, Text $issue) {
                return sprintf(
                    $this->issueUrl,
                    $issue,
                    $this->accountName,
                    $this->projectName,
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
        return 'pullRequest';
    }
}
