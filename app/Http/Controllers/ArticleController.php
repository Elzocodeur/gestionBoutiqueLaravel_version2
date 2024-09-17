<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\ArticleCollection;
use App\Http\Requests\StockArticleRequest;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Services\Interfaces\ArticleServiceInterface;

class ArticleController extends Controller
{
    protected $articeService;

    public function __construct(ArticleServiceInterface $articeService)
    {
        $this->articeService = $articeService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Article::class);

        return new ArticleCollection(
            $this->articeService->getAllArticles(
                $request->only(["disponible", "quantite", "libelle"])
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreArticleRequest $request)
    {
        $this->authorize('create', Article::class);

        return new ArticleResource($this->articeService->createArticle($request->validated()));
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $article = $this->articeService->getArticleById($id);

        $this->authorize('view', $article);

        return new ArticleResource($article);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateArticleRequest $request, int $id)
    {
        $article = $this->articeService->getArticleById($id);

        $this->authorize('update', $article);

        return new ArticleResource($this->articeService->updateArticle($id, $request->validated()));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $article = $this->articeService->getArticleById($id);

        $this->authorize('delete', $article);

        if ($this->articeService->deleteArticle($id))
            return ["message" => "l'article à bien été supprimer"];
        return ["message" => "L'article n'existe pas", "status" => 404];
    }

    public function libelle(Request $request)
    {
        $data = $request->validate(['libelle' => 'required|string']);
        $this->authorize('viewAny', Article::class);
        return new ArticleCollection($this->articeService->search($data));
    }

    public function stock(StockArticleRequest $request)
    {
        $this->authorize('viewAny', Article::class);

        return $this->articeService->stockArticles($request->validated());
    }



    public function incrementQuantity(Request $request, int $id)
    {
        $validatedData = $request->validate(['quantity' => 'required|integer|min:1']);

        $article = $this->articeService->getArticleById($id);

        $this->authorize('incrementQuantity', $article);

        return new ArticleResource($this->articeService->incrementQuantity($id, $validatedData["quantity"]));
    }
}
