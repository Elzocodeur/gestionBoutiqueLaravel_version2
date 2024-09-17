<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Facades\ArchiveDetteFacade;
use App\Http\Controllers\Controller;
use App\Facades\ArchiveDatabaseFacade;
use App\Http\Resources\DetteCollection;
use App\Http\Resources\DetteResource;

class ArchiveController extends Controller
{
    public function getArchive(Request $request)
    {
        $clientId = $request->query('client');
        $date = $request->query('date') ? new \DateTime($request->query('date')) : null;
        $detteArchiving = [];

        if ($date == null && $clientId == null) {
            $detteArchiving = ArchiveDetteFacade::getAll();
        } else {
            $detteArchiving = ArchiveDatabaseFacade::getAll($clientId, $date);
        }
        return new DetteCollection($detteArchiving);
    }

    public function getWithClient(Request $request, int $id)
    {
        $date = $request->query('date') ? new \DateTime($request->query('date')) : null;
        return new DetteCollection(ArchiveDatabaseFacade::getAll($id, $date));
    }


    public function getById(int $id)
    {
        $dette = ArchiveDatabaseFacade::getById($id);
        // dd($dette->load("articles"));
        if ($dette)
            return new DetteResource($dette);
        return ["message" => "Dette not found", "status" => 404];
    }

    public function restaureByDate(string $id)
    {
        $date = new \DateTime($id) ?? null;
        $dette = ArchiveDatabaseFacade::restoreByDate($date);
        if ($dette)
            return new DetteCollection($dette);
        return ["message" => "Dette not found for date", "status" => 404];
    }

    public function restaureById(int $id)
    {
        $dette = ArchiveDatabaseFacade::restore($id);
        if ($dette)
            return new DetteResource($dette);
        return ["message" => "Dette not found", "status" => 404];
    }

    public function restaureByClient(int $id)
    {
        $dette = ArchiveDatabaseFacade::restoreByClient($id);
        if (count($dette))
            return new DetteCollection($dette);
        return ["message" => "Dette not found", "status" => 404];
    }
}
