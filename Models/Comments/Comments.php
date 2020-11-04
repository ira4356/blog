<?php
namespace MyProject\Models\Comments;
use MyProject\Exceptions;
use MyProject\Models\ActiveRecordEntity;
use MyProject\Models\Articles\Article;
use MyProject\Models\Users\User;


class Comments extends \MyProject\Models\ActiveRecordEntity
{
    protected int $userId;
    protected int $articleId;
    protected $text;
    protected $createdAt;

    public function getUserId(): User
    {
        return User::getById($this->userId);
    }

    public function isAuthor($userId): bool
    {
        return $this->userId === $userId;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getArticleId(): int
    {
        return $this->articleId;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text)
    {
        $this->text = $text;
    }

    public function setAuthor(User $author): void
    {
        $this->userId = $author->getId();
    }

    public function setArticleId(Article $article): void
    {
        $this->articleId = $article->getId();
    }

    protected static function getTableName(): string
    {
        return 'comments';
    }



    public static function createFromArray(array $fields, User $author, Article $article): Comments
    {
        if (empty($fields['text'])) {
            throw new Exceptions\InvalidArgumentException('Не передан текст комментария');
        }

        $comment = new Comments();
        $comment->setAuthor($author);
        $comment->setArticleId($article);
        $comment->setText($fields['text']);
        $comment->save();
        return $comment;
    }

    public function updateFromArray(array $fields): Comments
    {
        if (empty($fields['text'])) {
            throw new Exceptions\InvalidArgumentException('Не передан текст комментария');
        }
        $this->setText($fields['text']);
        $this->save();
        return $this;
    }
}