<?php

namespace rmatil\CmsBundle\Features\Bootstrap;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\TableNode;
use Exception;
use rmatil\CmsBundle\Entity\ArticleCategory;

/**
 * Defines application features from the specific context.
 */
class ArticleCategoryContext implements Context, SnippetAcceptingContext {

    /**
     * @var array
     */
    private $articleCategories;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct() {
        $this->articleCategories = [];
    }

    /**
     * @Given the following ArticleCategories:
     *
     * @param \Behat\Gherkin\Node\TableNode $articleCategoryTable
     */
    public function theFollowingArticleCategories(TableNode $articleCategoryTable) {
        foreach ($articleCategoryTable->getHash() as $entry) {
            $category = new ArticleCategory();
            $category->setId($entry['id']);
            $category->setName($entry['name']);

            $this->articleCategories[$category->getId()] = $category;
        }
    }

    /**
     * @Then I expect the following ArticleCategories:
     *
     * @param \Behat\Gherkin\Node\TableNode $table
     *
     * @throws Exception If one of the objects is not the same
     */
    public function iExpectTheFollowingArticleCategories(TableNode $table) {
        foreach ($table->getHash() as $entry) {
            $categoryToCompare = new ArticleCategory();
            $categoryToCompare->setId($entry['id']);
            $categoryToCompare->setName($entry['name']);

            if ( ! (array_key_exists($entry['id'], $this->articleCategories) &&
                $this->articleCategories[$entry['id']] == $categoryToCompare)
            ) {
                throw new Exception(sprintf('Object with id "%s" is not the same', $entry['id']));
            }
        }
    }


}
