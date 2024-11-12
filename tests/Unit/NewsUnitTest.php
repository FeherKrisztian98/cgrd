<?php

namespace App\Tests\Unit;

use App\Model\News;
use App\Tests\TestCase;

/**
 * Unit tests for the news model
 */
class NewsUnitTest extends TestCase
{
    public function testNewsModelCanBeInstantiated()
    {
        $news = new News();
        assert($news instanceof News, 'Failed to instantiate News model');
    }

    public function testFromArray()
    {
        $news = News::fromArray([
            'title' => 'Test News',
            'content' => 'Test News',
        ]);

        assert($news->title === 'Test News', 'Failed to instantiate News model');
        assert($news->content === 'Test News', 'Failed to instantiate News model');
    }
}