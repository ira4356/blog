<?php
namespace MyProject\Controllers;
use MyProject\Models\Users\User;
use MyProject\Models\Articles\Article;
use MyProject\Models\ActiveRecordEntity;
use MyProject\Exceptions\Forbidden;


class AdminController extends AbstractController
{
    public function articlesViewForAdmin(User $user, array $articles)
    {
        if (!$user->isAdmin()) {
            throw new Forbidden('Для редактирования статьи необходимо обладать правами администратора');
        } else {
            try {

            }
        }
    }

}