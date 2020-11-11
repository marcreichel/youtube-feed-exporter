<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class YouTubeController extends Controller
{
    private array $subscriptions = [];

    public function redirectToProvider()
    {
        return Socialite::driver('youtube')->redirect();
    }

    public function handleProviderCallback()
    {
        $user = Socialite::driver('youtube')->user();

        $dbUser = User::updateOrCreate(
            [
                'id' => $user->getId(),
            ],
            [
                'avatar' => $user->getAvatar(),
                'token' => $user->token,
                'token_expires_at' => Carbon::now()->addSeconds($user->expiresIn),
            ],
        );

        Auth::login($dbUser);

        return redirect('/');
    }

    public function index()
    {
        if (!Auth::check()) {
            return redirect('/oauth/youtube/redirect');
        }

        $this->fetchSubscriptions();

        return [
            'app_version' => '0.20.0',
            'app_version_int' => 954,
            'subscriptions' => $this->subscriptions,
        ];
    }

    private function fetchSubscriptions(string $pageToken = null)
    {
        try {
            $client = new Client();
            if (isset(Auth::user()->token)) {
                $key = env('YOUTUBE_API_KEY');
                $params = http_build_query(collect([
                    'mine' => 'true',
                    'part' => 'snippet',
                    'maxResults' => '50',
                    'order' => 'alphabetical',
                    'key' => $key,
                    'pageToken' => $pageToken,
                ])->filter(function($item) {
                    return $item;
                })->toArray());
                $request = $client->get('https://youtube.googleapis.com/youtube/v3/subscriptions?' . $params, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . Auth::user()->token,
                        'Accept' => 'application/json',
                    ],
                ]);

                $response = json_decode($request->getBody()->getContents());

                $items = collect($response->items)
                    ->map(function($item) {
                        return [
                            'service_id' => 0,
                            'url' => 'https://www.youtube.com/channel/' . $item->snippet->resourceId->channelId,
                            'name' => $item->snippet->title,
                        ];
                    })
                    ->toArray();

                $this->subscriptions = [...$this->subscriptions, ...$items];

                if (property_exists($response, 'nextPageToken')) {
                    $this->fetchSubscriptions($response->nextPageToken);
                }
            }
        } catch (GuzzleException $e) {
        }
    }
}
