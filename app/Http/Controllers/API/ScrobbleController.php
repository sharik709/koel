<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\ScrobbleStoreRequest;
use App\Jobs\ScrobbleJob;
use App\Models\Song;
use App\Models\User;
use App\Services\LastfmService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Response;

class ScrobbleController extends Controller
{
    private $lastfmService;

    /** @var User */
    private $currentUser;

    public function __construct(LastfmService $lastfmService, Authenticatable $currentUser)
    {
        $this->lastfmService = $lastfmService;
        $this->currentUser = $currentUser;
    }

    public function store(ScrobbleStoreRequest $request, Song $song)
    {
        if (!$song->artist->is_unknown && $this->currentUser->connectedToLastfm()) {
            ScrobbleJob::dispatch($this->currentUser, $song, (int) $request->timestamp);
        }

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
