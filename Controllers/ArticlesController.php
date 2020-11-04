<?php
namespace MyProject\Controllers;
use MyProject\Exceptions\InvalidArgumentException;
use MyProject\Exceptions\NotFoundException;
use MyProject\Exceptions\UnauthorizedException;
use MyProject\Models\Articles\Article;
use MyProject\Models\Users\User;
use MyProject\Exceptions\Forbidden;
use MyProject\Models\Comments\Comments;

class ArticlesController extends AbstractController
{
    public function view(int $articleId)
    {
       $article = Article::getById($articleId);
       $comments = Comments::findAllByColumn('article_id', $articleId);

       if ($article === null) {
           throw new NotFoundException();
       }

        $this->view->renderHtml('articles/view.php', ['article' => $article, 'comments' => $comments]);
    }

    public function edit(int $articleId)
    {
        $article = Article::getById($articleId);
        if ($article === null) {
            throw new NotFoundException();
        }
        if ($this->user === null) {
            throw new UnauthorizedException();
        }
        if (!$this->user->isAdmin()) {
            throw new Forbidden('Для редактирования статьи необходимо обладать правами администратора');
        }
        if (!empty($_POST)) {
            try {
                $article->updateFromArray($_POST);
            } catch (InvalidArgumentException $e) {
                $this->view->renderHtml('articles/edit.php', ['error' => $e->getMessage(), 'article' => $article]);
                return;
            }
            header('Location: /test/articles/' . $article->getId(), true, 302);
            exit();
        }
        $this->view->renderHtml('articles/edit.php', ['article' => $article]);
    }

    public function create(): void
    {
        $author = User::getById(1);
        $article = new Article();
        $article->setName('Новое название статьи 3');
        $article->setText('Новый текст статьи 3');
        $article->setAuthor($author);
        $article->save();

        var_dump($article);
    }

    public function add(): void
    {
        if ($this->user === null) {
            throw new UnauthorizedException();
        }
        if ($this->user->getRole() !== 'admin') {
            throw new Forbidden('Для создания статьи нужно быть админом :-(');
        }
        if (!empty($_POST)) {
            try {
                $article = Article::createFromArray($_POST, $this->user);
            } catch (InvalidArgumentException $e) {
                $this->view->renderHtml('articles/add.php');
                return;
            }
            header('Location: /test/articles/' . $article->getId(), true, 302);
            exit();
        }
        $this->view->renderHtml('articles/add.php');
    }

    public function delete(int $id)
    {
        $article = Article::getById($id);
        if ($article !== null) {
            $article->delete();
            echo 'Статья удалена';
            var_dump($article);
        } else {
            $this->view->renderHtml('errors/404.php', [], 404);
        }
    }
}