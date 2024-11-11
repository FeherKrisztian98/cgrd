<?php

namespace Model;

/** News model class representing a news article */
class News extends AbstractModel
{
    /** @var string The title of the news article */
    public string $title;

    /** @var string The content of the news article */
    public string $content;

    /** @var string The table name in the database */
    protected const string TABLE_NAME = 'news_articles';
}