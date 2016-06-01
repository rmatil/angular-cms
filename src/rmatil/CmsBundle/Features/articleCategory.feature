Feature: ArticleCategory
  In order to organize articles
  As an administrator
  I need to be able to create ArticleCategories


  Scenario: Creating a new ArticleCategory
    Given the following ArticleCategories:
      | id | name        |
      | 1  | Default     |
      | 2  | Umlaute äöü |
    Then I expect the following ArticleCategories:
      | id | name        |
      | 1  | Default     |
      | 2  | Umlaute äöü |
