<?php

namespace App\Tests\Unit;

use App\Model\News;
use App\Tests\TestCase;

/**
 * Unit tests for the news model
 */
class NewsUnitTest extends TestCase
{
    /**
     * Test for the constructor
     *
     * @return void
     */
    public function testNewsModelCanBeInstantiated(): void
    {
        $news = new News();
        assert($news instanceof News, 'Failed to instantiate News model');
    }

    /**
     * Test for creating from array
     *
     * @return void
     */
    public function testFromArray(): void
    {
        $news = News::fromArray([
            'title' => 'Test News',
            'content' => 'Test News',
        ]);

        assert($news->title === 'Test News', 'Failed to instantiate News model');
        assert($news->content === 'Test News', 'Failed to instantiate News model');
    }
}