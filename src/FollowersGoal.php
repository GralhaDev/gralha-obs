<?php

namespace GralhaObs;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class FollowersGoal
{
    public static function show($goal)
    {
        $current = self::getFollowers();
        print(sprintf(
            self::template(),
            $current->total / $goal * 100,
            $current->total,
            $goal,
        ));
    }

    private static function template()
    {
        // os front pira
        return <<<TEMPLATE
            <div style="position: relative; background: white; border:2px solid #333; border-radius: 4px;">
                <div style="background: #474A8A; width: %s%%; padding: 3px 0; border-radius: 1px;">&nbsp;</div>
                <div style="font-family: sans-serif; position:absolute; top: 0; left: 0; padding: 3px;">Seguidores</div>
                <div style="font-family: sans-serif; position:absolute; top: 0; left: 50%%; padding: 3px 0; width: 6%%; margin-right: -3%%;">%s</div>
                <div style="font-family: sans-serif; position:absolute; top: 0; right: 0; padding: 3px;">%s</div>
            </div>
            <script>setTimeout(function(){location.reload()}, 60000)</script>
        TEMPLATE;
    }

    private function getFollowers()
    {
        $credentials = new \Config\Credentials;

        $httpClient = new Client;

        $response = $httpClient->post(
            'https://id.twitch.tv/oauth2/token',
            [
                RequestOptions::QUERY => [
                    'client_id'     => $credentials->client_id,
                    'client_secret' => $credentials->client_secret,
                    'grant_type'    => 'client_credentials',
                    'scope'         => 'channel_read',
                ],
            ]
        );

        $jsonResponse = \GuzzleHttp\json_decode($response->getBody()->getContents(), false);

        $followersResponse = $httpClient->get(
            'https://api.twitch.tv/helix/users/follows',
            [
                RequestOptions::QUERY   => [
                    'to_id' => $credentials->user_id,
                ],
                RequestOptions::HEADERS => [
                    'Authorization' => 'Bearer ' . $jsonResponse->access_token,
                    'client-id'     => $credentials->client_id,
                ],
            ]
        );

        return \GuzzleHttp\json_decode($followersResponse->getBody()->getContents(), false);
    }
}
