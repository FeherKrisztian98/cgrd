<?php

namespace App\Tests\Integration;

use App\Database\Database;
use App\Model\News;
use App\Tests\TestCase;

/**
 * Integration tests for the news model
 */
class NewsIntegrationTest extends TestCase
{
    /**
     * Start a transaction so DB data isn't affected
     *
     * @return void
     */
    protected function setup(): void
    {
        Database::getInstance()->startTransaction();
    }

    /**
     * After a test is done, roll back changes to the DB
     *
     * @return void
     */
    protected function cleanup(): void
    {
        Database::getInstance()->rollBack();
    }

    /**
     * A test for saving news
     *
     * @return void
     */
    public function testSaveNews(): void
    {
        $news = new News();
        $news->title = 'Test save';
        $news->content = 'Test save';
        $news->save();

        assert($news->getId() > 0, 'Failed to save News model');
    }

    /**
     * A test for deleting news
     *
     * @return void
     */
    public function testDeleteNews(): void
    {
        $news = new News();
        $news->title = 'Test delete';
        $news->content = 'Test delete';
        $news->save();

        $newsId = $news->getId();

        News::deleteById($newsId);

        $deletedNews = new News($newsId);
        assert(empty($deletedNews->title), 'Failed to delete the News model');
    }

    /**
     * A test for updating news
     *
     * @return void
     */
    public function testUpdateNews(): void
    {
        $news = new News();
        $news->title = 'Title to Update';
        $news->content = 'Content to update';

        $news->save();

        $newsId = $news->getId();

        $news->title = 'Updated title';
        $news->content = 'Updated content';

        $news->save();

        $updatedNews = new News($newsId);

        assert($updatedNews->title === 'Updated title', 'Failed to update the title of the News model');
        assert($updatedNews->content === 'Updated content', 'Failed to update the content of the News model');
    }
}