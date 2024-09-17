<?php

namespace App\Repository;

use App\Exceptions\RepositoryException;
use App\Models\Article;
use Illuminate\Database\Eloquent\Collection;
use App\Repository\Interfaces\ArticleRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ArticleRepository implements ArticleRepositoryInterface
{

    protected $model;

    public function __construct(Article $model)
    {
        $this->model = $model;
    }

    /**
     * Récupère tous les articles.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all($filter = null): Collection
    {
        if ($filter)
            return $this->search($filter);
        return $this->model->all();
    }

    /**
     * Trouve un article par son ID.
     *
     * @param  int  $id
     * @return \App\Models\Article|null
     */
    public function find(int $id): ?Article
    {
        try {
            return $this->model->find($id);
        } catch (\Exception $e) {
            throw new RepositoryException('Erreur lors de la recherche de l\'article: ' . $e->getMessage());
        }
    }

    /**
     * Crée un nouvel article.
     *
     * @param  array  $attributes
     * @return \App\Models\Article
     */
    public function create(array $attributes): Article
    {
        try {
            return $this->model->create($attributes);
        } catch (\Exception $e) {
            throw new RepositoryException('Erreur lors de la création de l\'article: ' . $e->getMessage());
        }
    }

    /**
     * Met à jour un article existant.
     *
     * @param  int  $id
     * @param  array  $attributes
     * @return \App\Models\Article
     */
    public function update(int $id, array $attributes): Article
    {
        try {
            $article = $this->model->findOrFail($id);
            $article->update($attributes);
            return $article;
        } catch (\Exception $e) {
            throw new RepositoryException('Erreur lors de la mise à jour de l\'article: ' . $e->getMessage());
        }
    }

    /**
     * Supprime un article par son ID.
     *
     * @param  int  $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        try {
            $article = $this->model->findOrFail($id);
            return $article->delete();
        } catch (\Exception $e) {
            throw new RepositoryException('Erreur lors de la suppression de l\'article: ' . $e->getMessage());
        }
    }

    public function search(array $criteria)
    {
        try {

            $query = $this->model->query();

            if (isset($criteria['libelle'])) {

                $query->byLibelle($criteria['libelle']);
            }

            if (isset($criteria['quantite'])) {
                $query->available($criteria['quantite']);
            }

            if (isset($criteria["disponible"])) {
                $query->isDisponible($criteria["disponible"]);
            }

            return $query->get();
        } catch (\Exception $e) {
            throw new RepositoryException('Erreur lors de la recherche des articles: ' . $e->getMessage());
        }
    }

    public function incrementQuantity(int $id, int $quantity)
    {
        try {
            $article = $this->model->findOrFail($id);
            $article->increment('quantity', $quantity);
            return $article;
        } catch (\Exception $e) {
            throw new RepositoryException("Erreur lors de l'approvisionnement de l'articles: " . $e->getMessage());
        }
    }

    /**
     * Met à jour le stock des articles.
     *
     * @param array $data
     * @param Collection $articles
     * @return array
     * @throws RepositoryException
     */
    public function updateArticleStock(array $data, Collection $articles): array
    {
        $failed = [];
        $success = [];
        DB::transaction(function () use ($data, $articles, &$failed, &$success) {
            foreach ($data['articles'] as $item) {

                if (!isset($articles[$item['id']])) {
                    $failed[] = [
                        'id' => $item['id'],
                        'error' => 'Article non trouvé.',
                    ];
                    continue;
                }

                $article = $articles[$item['id']];
                if ($item['quantity'] > 0) {
                    $article->increment('quantity', $item['quantity']);
                    $success[] = $article;
                } else {
                    $failed[] = [
                        'id' => $item['id'],
                        'error' => 'La quantité doit être supérieure à 0.',
                    ];
                }
            }
        });

        return ['success' => $success, 'failed' => $failed];
    }

    /**
     * Récupère les articles par leurs IDs.
     *
     * @param array $articleIds
     * @return Collection
     * @throws RepositoryException
     */
    public function getArticlesByIds(array $articleIds): Collection
    {
        try {
            return $this->model->whereIn('id', $articleIds)->get()->keyBy('id');
        } catch (\Exception $e) {
            throw new RepositoryException('Erreur lors de la récupération des articles: ' . $e->getMessage());
        }
    }

    public function avalable(int $id, float $quantity)
    {
        try {
            $article = $this->find($id);
            if (!$article) {
                return false;
            }
            if (($article->quantity - $article->seuil) >= $quantity)
                return true;
            return false;
        } catch (\Exception $e) {
            throw new RepositoryException("Cette articles avec l'id $id n'existe pas");
        }
    }
}
