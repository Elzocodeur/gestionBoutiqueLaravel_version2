<?php

namespace App\Services\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Article;

interface ArticleServiceInterface
{
    /**
     * Récupère tous les articles avec des filtres optionnels.
     *
     * @param array|null $filter
     * @return Collection
     */
    public function getAllArticles(?array $filter = null): Collection;

    /**
     * Récupère un article par son ID.
     *
     * @param int $id
     * @return Article|null
     */
    public function getArticleById(int $id): ?Article;

    /**
     * Crée un nouvel article.
     *
     * @param array $data
     * @return Article
     */
    public function createArticle(array $data): Article;

    /**
     * Met à jour un article existant.
     *
     * @param int $id
     * @param array $data
     * @return Article
     */
    public function updateArticle(int $id, array $data): Article;

    /**
     * Supprime un article par son ID.
     *
     * @param int $id
     * @return bool
     */
    public function deleteArticle(int $id): bool;

    public function incrementQuantity(int $id, int $quantity);

    public function stockArticles(array $data): array;

    public function search($criteria);

}
