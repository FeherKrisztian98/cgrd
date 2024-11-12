<?php

namespace App\Controller;

use App\HTTP\Response;
use App\Model\News;

/**
 * Handles the news management functionality
 */
class NewsController extends AbstractController
{
    /**
     * Lists all news articles
     *
     * @return Response
     */
    public function listNews(): Response
    {
        $news = News::findAll();

        return $this->response->view('NewsController/news', [
            'news' => $news,
        ]);
    }

    /**
     * Creates a new news article
     *
     * @return Response
     */
    public function createNews(): Response
    {
        $title = $this->request->getParam('title');
        $content = $this->request->getParam('content');

        $item = new News();
        $item->title = $title;
        $item->content = $content;
        $item->save();

        return $this->response
            ->setNotification('News was successfully created!')
            ->redirect('news');
    }

    /**
     * Displays the modification form for an existing news article
     *
     * @return Response
     */
    public function modifyNews(): Response
    {
        $id = $this->request->getParam('id');

        $news = News::findAll();

        $article = $news[$id] ?? null;

        if ($article === null) {
            return $this->response->redirect('/news', true);
        }

        return $this->response->view('NewsController/modifyNews', [
            'news' => $news,
            'id' => $article->getId(),
            'title' => $article->title,
            'content' => $article->content,
        ]);
    }

    /**
     * Updates an existing news article
     *
     * @return Response
     */
    public function updateNews(): Response
    {
        $id = $this->request->getParam('id');
        $title = $this->request->getParam('title');
        $content = $this->request->getParam('content');

        $news = new News($id);
        $news->title = $title;
        $news->content = $content;
        $news->save();

        return $this->response
            ->setNotification('News was successfully changed!')
            ->redirect('/news');
    }

    /**
     * Deletes an existing news article
     *
     * @return Response
     */
    public function deleteNews(): Response
    {
        $id = $this->request->getParam('id');

        News::deleteById($id);

        return $this->response
            ->setNotification('News was deleted!')
            ->redirect('news');
    }
}