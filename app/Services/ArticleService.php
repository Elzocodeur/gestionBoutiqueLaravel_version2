<?php

namespace App\Services;

use App\Services\Interfaces\ArticleServiceInterface;
use App\Repository\Interfaces\ArticleRepositoryInterface;
use App\Models\Article;
use Illuminate\Database\Eloquent\Collection;
use App\Exceptions\ServiceException;

class ArticleService implements ArticleServiceInterface
{
    protected $articleRepository;

    public function __construct(ArticleRepositoryInterface $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    /**
     * Récupère tous les articles avec des filtres optionnels.
     *
     * @param array|null $filter
     * @return Collection
     * @throws ServiceException
     */
    public function getAllArticles(?array $filter = null): Collection
    {
        try {
            return $this->articleRepository->all($filter);
        } catch (\Exception $e) {
            throw new ServiceException('Erreur lors de la récupération des articles: ' . $e->getMessage());
        }
    }

    /**
     * Récupère un article par son ID.
     *
     * @param int $id
     * @return Article|null
     * @throws ServiceException
     */
    public function getArticleById(int $id): ?Article
    {
        try {
            return $this->articleRepository->find($id);
        } catch (\Exception $e) {
            throw new ServiceException('Erreur lors de la récupération de l\'article: ' . $e->getMessage());
        }
    }

    /**
     * Crée un nouvel article.
     *
     * @param array $data
     * @return Article
     * @throws ServiceException
     */
    public function createArticle(array $data): Article
    {
        try {
            return $this->articleRepository->create($data);
        } catch (\Exception $e) {
            throw new ServiceException('Erreur lors de la création de l\'article: ' . $e->getMessage());
        }
    }

    /**
     * Met à jour un article existant.
     *
     * @param int $id
     * @param array $data
     * @return Article
     * @throws ServiceException
     */
    public function updateArticle(int $id, array $data): Article
    {
        try {
            return $this->articleRepository->update($id, $data);
        } catch (\Exception $e) {
            throw new ServiceException('Erreur lors de la mise à jour de l\'article: ' . $e->getMessage());
        }
    }

    /**
     * Supprime un article par son ID.
     *
     * @param int $id
     * @return bool
     * @throws ServiceException
     */
    public function deleteArticle(int $id): bool
    {
        try {
            return $this->articleRepository->delete($id);
        } catch (\Exception $e) {
            throw new ServiceException('Erreur lors de la suppression de l\'article: ' . $e->getMessage());
        }
    }

    public function incrementQuantity($id, $quantity)
    {
        try {
            return $this->articleRepository->incrementQuantity($id, $quantity);
        } catch (\Exception $e) {
            throw new ServiceException("Erreur lors de l'approvisionnement de l'article: " . $e->getMessage());
        }
    }

    /**
     * Gère l'approvisionnement du stock d'articles.
     *
     * @param array $data
     * @return array
     * @throws ServiceException
     */
    public function stockArticles(array $data): array
    {
        $articleIds = array_column($data['articles'], 'id');

        
        try {
            $articles = $this->articleRepository->getArticlesByIds($articleIds);
            return $this->articleRepository->updateArticleStock($data, $articles);
        } catch (\Exception $e) {
            throw new ServiceException('Erreur lors de l\'approvisionnement des articles: ' . $e->getMessage());
        }
    }

    public function search($criteria) {
        try {
            return $this->articleRepository->search($criteria);
        } catch (\Exception $e) {
            throw new ServiceException("Erreur lors de la recherche d'un article: " . $e->getMessage());
        }
    }
}