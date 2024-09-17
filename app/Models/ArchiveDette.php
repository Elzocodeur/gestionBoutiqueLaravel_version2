<?php

namespace App\Models;

use DateTime;
use Carbon\Carbon;
use App\Models\Dette;
use App\Models\Article;
use App\Models\Paiement;
use Illuminate\Support\Facades\Log;
use App\Facades\DetteRepositoryFacade;

class ArchiveDette
{
    private ?int $detteId;
    private float $montant;
    private array $client;
    private array $articles;
    private array $paiements;
    private Carbon $date;
    private Carbon $archivedAt;

    /**
     * ArchiveDette constructor.
     * 
     * @param array|Dette $data
     */
    public function __construct(array|Dette $data = [])
    {
        if ($data instanceof Dette) {
            $archive = self::create($data);
            $this->detteId = $archive->detteId;
            $this->montant = $archive->montant;
            $this->client = $archive->client;
            $this->articles = $archive->articles;
            $this->paiements = $archive->paiements;
            $this->date = $archive->date;
            $this->archivedAt = $archive->archivedAt;
        } else {
            $this->detteId = $data['dette_id'] ?? null;
            $this->montant = $data['montant'] ?? 0.0;
            $this->client = $data['client'] ?? [];
            $this->articles = $data['articles'] ?? [];
            $this->paiements = $data['paiements'] ?? [];
            $this->date = isset($data['date']) ? Carbon::parse($data['date']) : Carbon::now();
            $this->archivedAt = isset($data['archived_at']) ? Carbon::parse($data['archived_at']) : Carbon::now();
        }
    }

    // Vérifie si l'archive contient une dette avec l'ID spécifié
    public function containsDette(string $detteId): bool
    {
        return $this->detteId == $detteId;
    }

    public function getId(): ?string
    {
        return (string)$this->detteId;
    }

    /**
     * Convert ArchiveDette to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'dette_id' => $this->detteId,
            'montant' => $this->montant,
            'client' => $this->client,
            'articles' => $this->articles,
            'paiements' => $this->paiements,
            'date' => $this->date->toDateTimeString(),
            'archived_at' => $this->archivedAt->toDateTimeString(),
        ];
    }

    /**
     * Format an article to an array.
     * 
     * @param Article $article
     * @return array
     */
    public function formatArticle(Article $article): array
    {
        return [
            'id' => $article->id,
            'libelle' => $article->libelle,
            'quantity' => $article->pivot->quantity ?? 0,
            'price' => $article->pivot->price ?? 0.0,
        ];
    }

    public function formatPaiement(Paiement $paiement): array
    {
        return [
            'client_id' => $paiement->client_id,
            'montant' => $paiement->montant,
            'date' => $paiement->date,
            // 'dette_id' => $paiement->dette_id
        ];
    }

    /**
     * Format a dette to an array.
     * 
     * @param Dette $dette
     * @return array
     */
    public function formatDette(Dette $dette): array
    {
        return [
            'id' => $dette->id,
            'montant' => $dette->montant,
            'date' => $dette->date->toDateTimeString(),
            'client' => [
                'id' => $dette->client_id,
                'user_id' => $dette->client->user_id ?? null,
            ],
        ];
    }

    /**
     * Create an ArchiveDette from a Dette instance.
     * 
     * @param Dette $dette
     * @return ArchiveDette
     */
    public static function create(Dette $dette): self
    {
        $articlesFormatted = $dette->articles->map(function (Article $article) {
            return (new self())->formatArticle($article);
        })->toArray();

        $paiementsFormatted = $dette->paiements->map(function (Paiement $paiement) {
            return (new self())->formatPaiement($paiement);
        })->toArray();

        $archiveData = [
            'dette_id' => $dette->id,
            'montant' => $dette->montant,
            'client' => [
                'id' => $dette->client_id,
                'user_id' => $dette->client->user_id ?? null,
            ],
            'articles' => $articlesFormatted,
            'paiements' => $paiementsFormatted,
            'date' => $dette->date->toDateTimeString(),
            'archived_at' => Carbon::now()->toDateTimeString(),
        ];

        return new self($archiveData);
    }

    /**
     * Rebuild a Dette model from ArchiveDette data.
     * 
     * @return Dette
     */
    public function toDette(): Dette
    {
        $dette = new Dette();
        $dette->id = $this->detteId;
        $dette->montant = $this->montant;
        $dette->client_id = $this->client['id'] ?? null;
        $dette->date = $this->date;
        $dette->archive_at = $this->archivedAt;
        return $dette;
    }



    public function withRelation(Dette $dette, array $relations): Dette
    {
        // $dette->date = new DateTime($dette->date);
        if (in_array('articles', $relations)) {
            $articlesCollection = collect($this->articles)->map(function ($article) {
                return new Article($article);
            });
            $dette->setRelation('articles', $articlesCollection);
        }

        if (in_array('paiements', $relations)) {
            $paiementsCollection = collect($this->paiements)->map(function ($paiement) {
                return new Paiement($paiement);
            });
            $dette->setRelation('paiements', $paiementsCollection);
        }

        return $dette;
    }



    public static function attach(Dette $dette, array $articles, ?array $paiements = null)
    {
        $dette->setRelation("articles", collect($articles)->map(function ($article) {
            return new Article($article);
        }));
        
        if(!empty($paiements))
        {
            $dette->setRelation("paiements", collect($paiements)->map(function ($paiement) {
                return new Paiement($paiement);
            }));
        }
        return $dette;
    }


    /**
     * Magic method to get private properties.
     * 
     * @param string $property
     * @return mixed
     * @throws \Exception
     */
    public function __get(string $property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }

        throw new \Exception("Property {$property} does not exist.");
    }
}
