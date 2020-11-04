<?php

namespace MyProject\Controllers;

use MyProject\Exceptions\InvalidArgumentException;
use MyProject\Exceptions\NotFoundException;
use MyProject\Exceptions\UnauthorizedException;
use \MyProject\Models\Articles\Article;
use MyProject\Models\Comments\Comments;
use \MyProject\Models\Users\User;

class CommentsController extends AbstractController
{
    public function addComment($articleId): void
    {
        if ($this->user === null) {
            throw new UnauthorizedException();
        }
        $article = Article::getById($articleId);
        if ($article === null) {
            throw new NotFoundException();
        }
        if (!empty($_POST)) {
            try {
                $comment = Comments::createFromArray($_POST, $this->user, $article);
            } catch (InvalidArgumentException $e) {
                $this->view->renderHtml('comments/add.php', ['article' => $article]);
                return;
            }
            header('Location: /test/articles/' . $article->getId(), true, 302);
            exit();
        }
        $this->view->renderHtml('comments/add.php', ['article' => $article]);
    }

    public function editComment(int $id)
    {
        $comment = Comments::getById($id);
        if ($comment === null) {
            throw new NotFoundException();
        }
        if (!empty($_POST)) {
            try {
                $comment->updateFromArray($_POST);
            } catch (InvalidArgumentException $e) {
                $this->view->renderHtml('comments/edit.php', ['error' => $e->getMessage(), 'comment' => $comment]);
                return;
            }
            header('Location: /test/articles/' . $comment->getArticleId(), true, 302);
            exit();
        }
        $this->view->renderHtml('comments/edit.php', ['comment' => $comment]);
    }
}