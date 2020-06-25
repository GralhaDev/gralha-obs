<?php

namespace GralhaObs;

use Config\Credentials;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class SubscribersGoal
{
    public static function show($goal): void
    {
        $current = self::getSubscribers();
        print(sprintf(
            self::template(),
            \count($current->data) / $goal * 100,
            \count($current->data),
            $goal,
        ));
    }

    private static function template(): string
    {
        // os front pira
        return <<<TEMPLATE
            <div style="position: relative; background: white; border:2px solid #333; border-radius: 4px;">
                <div style="background: #474A8A; width: %s%%; padding: 3px 0; border-radius: 1px;">&nbsp;</div>
                <div style="font-family: sans-serif; position:absolute; top: 0; left: 0; padding: 3px;">Subscribers</div>
                <div style="font-family: sans-serif; position:absolute; top: 0; left: 50%%; padding: 3px 0; width: 6%%; margin-right: -3%%;">%s</div>
                <div style="font-family: sans-serif; position:absolute; top: 0; right: 0; padding: 3px;">%s</div>
            </div>
            <script>setTimeout(function(){location.reload()}, 60000)</script>
        TEMPLATE;
    }

    private static function getSubscribers()
    {
        $credentials = new Credentials;

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
            'https://api.twitch.tv/helix/subscriptions',
            [
                RequestOptions::QUERY   => [
                    'broadcaster_id' => $credentials->user_id,
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
