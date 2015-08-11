Feature: Posts
  In order to publish content
  As a service consumer
  I need to be able to create posts and publish them online

  Scenario Outline: Creating a new post with valid data
    Given No post exists with id <id>
    When I call create post with id <id>, title <title> and content <content>
    Then I should see a PostWasCreated event with id <id>, title <title> and content <content>

    Examples:
      | id                                      | title              | content              |
      | "25769c6c-d34d-4bfe-ba98-e0ee856f3e7a"  | "My title"         | "My content"         |
      | "25769c6c-d34d-4bfe-ba98-e0ee856f3e7a"  | "My <b>title</b>"  | "My <b>content</b>"  |


  Scenario Outline: Creating a new post with invalid data
    Given No post exists with id <id>
    When I call create post with id <id>, title <title> and content <content>
    Then I should see an Exception
     And No new events should have been recorded

    Examples:
      | id                                     | title       | content        |
      | 1234                                   | ""          | "My content"   |
      | ""                                     | "My title"  | "My content"   |
      | "  "                                   | "My title"  | "My content"   |
      | "\n"                                   | "My title"  | "My content"   |
      | null                                   | "My title"  | "My content"   |
      | "25769c6c-d34d-4bfe-ba98-e0ee856f3e7a" | ""          | "My content"   |
      | "25769c6c-d34d-4bfe-ba98-e0ee856f3e7a" | " "         | "My content"   |
      | "25769c6c-d34d-4bfe-ba98-e0ee856f3e7a" | "\n"        | "My content"   |
      | "25769c6c-d34d-4bfe-ba98-e0ee856f3e7a" | null        | "My content"   |
      | "25769c6c-d34d-4bfe-ba98-e0ee856f3e7a" | "My title"  | ""             |
      | "25769c6c-d34d-4bfe-ba98-e0ee856f3e7a" | "My title"  | " "            |
      | "25769c6c-d34d-4bfe-ba98-e0ee856f3e7a" | "My title"  | "\n"           |
      | "25769c6c-d34d-4bfe-ba98-e0ee856f3e7a" | "My title"  | null           |


  Scenario Outline: Changing post title with valid title
    Given A post with id <id> exists with title <title> and content <content>
    When I call changeTitle with <newTitle>
    Then I should see a PostTitleWasChanged event with id <id> and title <newTitle>

    Examples:
      | id                                      | title    | content       | newTitle     |
      | "25769c6c-d34d-4bfe-ba98-e0ee856f3e7a"  | "Title"  | "My content"  | "New title"  |


  Scenario Outline: Changing post title with invalid title
    Given A post with id <id> exists with title <title> and content <content>
    When I call changeTitle with <newTitle>
    Then I should see an Exception
     And No new events should have been recorded

    Examples:
      | id                                      | title    | content       | newTitle     |
      | "25769c6c-d34d-4bfe-ba98-e0ee856f3e7a"  | "Title"  | "My content"  | ""           |
      | "25769c6c-d34d-4bfe-ba98-e0ee856f3e7a"  | "Title"  | "My content"  | "  "         |
      | "25769c6c-d34d-4bfe-ba98-e0ee856f3e7a"  | "Title"  | "My content"  | "\n"         |
      | "25769c6c-d34d-4bfe-ba98-e0ee856f3e7a"  | "Title"  | "My content"  | null         |


  Scenario Outline: Changing post content
    Given A post with id <id> exists with title <title> and content <content>
    When I call changeContent with <newContent>
    Then I should see a PostContentWasChanged event with id <id> and content <newContent>

    Examples:
      | id                                      | title    | content       | newContent     |
      | "25769c6c-d34d-4bfe-ba98-e0ee856f3e7a"  | "Title"  | "My content"  | "New content"  |


  Scenario Outline: Changing post content with invalid content
    Given A post with id <id> exists with title <title> and content <content>
    When I call changeContent with <newContent>
    Then I should see an Exception
     And No new events should have been recorded

    Examples:
      | id                                      | title    | content       | newContent     |
      | "25769c6c-d34d-4bfe-ba98-e0ee856f3e7a"  | "Title"  | "My content"  | ""             |
      | "25769c6c-d34d-4bfe-ba98-e0ee856f3e7a"  | "Title"  | "My content"  | "  "           |
      | "25769c6c-d34d-4bfe-ba98-e0ee856f3e7a"  | "Title"  | "My content"  | "\n"           |
      | "25769c6c-d34d-4bfe-ba98-e0ee856f3e7a"  | "Title"  | "My content"  | null           |

  Scenario Outline: Publishing a post
    Given A post with id <id> exists with title <title> and content <content>
    When I call publish
    Then I should see a PostWasPublished event with id <id>

    Examples:
      | id                                      | title    | content       |
      | "25769c6c-d34d-4bfe-ba98-e0ee856f3e7a"  | "Title"  | "My content"  |

  Scenario Outline: Publishing an already published post
    Given A post with id <id> exists with title <title> and content <content>
      And Post is published
    When I call publish
    Then No new events should have been recorded

    Examples:
      | id                                      | title    | content       |
      | "25769c6c-d34d-4bfe-ba98-e0ee856f3e7a"  | "Title"  | "My content"  |

  Scenario Outline: Unpublishing a post
    Given A post with id <id> exists with title <title> and content <content>
      And Post is published
    When I call unpublish
    Then I should see a PostWasUnpublished event with id <id>

    Examples:
      | id                                      | title    | content       |
      | "25769c6c-d34d-4bfe-ba98-e0ee856f3e7a"  | "Title"  | "My content"  |

  Scenario Outline: Unpublishing an unpublished post
    Given A post with id <id> exists with title <title> and content <content>
    When I call unpublish
    Then No new events should have been recorded

    Examples:
      | id                                      | title    | content       |
      | "25769c6c-d34d-4bfe-ba98-e0ee856f3e7a"  | "Title"  | "My content"  |

