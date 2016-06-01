<?php

namespace rmatil\CmsBundle\Features\Bootstrap;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use rmatil\CmsBundle\Entity\Article;

/**
 * Defines application features from the specific context.
 */
class ArticleContext implements Context, SnippetAcceptingContext {

    /**
     * @var Article
     */
    private $article;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct() {
        $this->article = new Article();
    }

    /**
     * @Given there is a(n) :arg1 having as value :arg2
     *
     * @param $arg1 string The first argument
     * @param $arg2 string The second argument
     */
    public function thereIsAndHavingAsValueSth($arg1, $arg2) {
        $methodName = sprintf('set%s', ucfirst($arg1));

        if (method_exists($this->article, $methodName)) {
            $this->article->{$methodName}($arg2);
        } else {
            throw new \RuntimeException("Could not assert " . $arg1);
        }
    }

    /**
     * @Then the :arg1 should be :arg2
     */
    public function theShouldBe($arg1, $arg2) {
//        PHPUnit_Framework_Assert::assertCount(
//            intval($count),
//            $this->basket
//        );
    }
}
