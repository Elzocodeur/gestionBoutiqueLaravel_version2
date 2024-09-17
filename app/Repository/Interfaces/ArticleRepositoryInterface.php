<?php

namespace App\Repository\Interfaces;

use App\Models\Article;
use Illuminate\Database\Eloquent\Collection;

interface ArticleRepositoryInterface
{
    /**
     * Récupère tous les articles.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all($filter = null): Collection;
    /**
     * Trouve un article par son ID.
     *
     * @param  int  $id
     * @return \App\Models\Article|null
     */
    public function find(int $id): ?Article;

    /**
     * Crée un nouvel article.
     *
     * @param  array  $attributes
     * @return \App\Models\Article
     */
    public function create(array $attributes): Article;

    /**
     * Met à jour un article existant.
     *
     * @param  int  $id
     * @param  array  $attributes
     * @return \App\Models\Article
     */
    public function update(int $id, array $attributes): Article;

    /**
     * Supprime un article par son ID.
     *
     * @param  int  $id
     * @return bool
     */
    public function delete(int $id): bool;


    public function incrementQuantity(int $id, int $quantity);

    public function updateArticleStock(array $data, Collection $articles): array;

    /**
     * Récupère les articles par leurs IDs.
     *
     * @param array $articleIds
     * @return Collection
     * @throws RepositoryException
     */
    public function getArticlesByIds(array $articleIds): Collection;


    public function search(array $criteria);

    public function avalable(int $id, float $quantity);

}
