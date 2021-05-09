<?php

namespace App\Services;

use App\Model\MinutisId;
use App\Model\MinutisToken;
use App\Settings;
use Bundles\SettingsBundle\Manager\SettingManager;
use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;

class Minutis
{
    /**
     * @var SettingManager
     */
    private $settingManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Client
     */
    private $client;

    public function __construct(SettingManager $settingManager, LoggerInterface $logger)
    {
        $this->settingManager = $settingManager;
        $this->logger         = $logger;
    }

    public function searchForOperations(string $structureExternalId, string $criteria = null) : array
    {
        $response = $this->getClient()->get('/api/regulation', $this->populateAuthentication([
            'query' => [
                'parentExternalId' => sprintf('red_cross_france_leaf_%s', $structureExternalId),
                'nom'              => $criteria,
            ],
        ]));

        return array_map(function (array $row) {
            return [
                'id'    => $row['id'],
                'human' => sprintf('%s (%s)', $row['nom'], $row['owner']),
            ];
        }, json_decode($response->getBody()->getContents(), true));
    }

    public function searchForVolunteer(string $volunteerExternalId) : ?array
    {
        $nivol = str_pad($volunteerExternalId, 12, '0', STR_PAD_LEFT);

        $response = $this->getClient()->get('/api/ressource', $this->populateAuthentication([
            'query' => [
                'type'       => 'benevole',
                'externalId' => $nivol,
            ],
        ]));

        $result = json_decode($response->getBody()->getContents(), true)['entities'];

        if (!$result) {
            $this->logger->error(sprintf('Cannot find volunteer with external id "%s" in Minutis', $nivol));

            return null;
        }

        return reset($result);
    }

    public function createOperation(string $structureExternalId, string $name, string $ownerEmail) : MinutisId
    {
        $response = $this->getClient()->post('/api/regulation', $this->populateAuthentication([
            'json' => [
                'parentExternalId' => sprintf('red_cross_france_leaf_%s', $structureExternalId),
                'nom'              => $name,
                'owner'            => $ownerEmail,
            ],
        ]));

        $payload = json_decode($response->getBody()->getContents(), true);

        return new MinutisId($payload['id'], $payload['publicId']);
    }

    public function addResourceToOperation(int $externalOperationId, string $volunteerExternalId)
    {
        $resource = $this->searchForVolunteer($volunteerExternalId);

        if (!$resource) {
            return;
        }

        $this->getClient()->post(sprintf('/api/regulation/%d/ressource', $externalOperationId), $this->populateAuthentication([
            'json' => [
                'locked'       => true,
                'regulationId' => $externalOperationId,
                'ressource'    => $resource,
            ],
        ]));
    }

    private function populateAuthentication(array $config)
    {
        return array_merge_recursive($config, [
            'headers' => [
                'Authorization' => sprintf('Bearer %s', $this->getToken()),
            ],
        ]);
    }

    private function getToken() : MinutisToken
    {
        $cypher = $this->settingManager->get(Settings::MINUTIS_TOKEN);
        if (!$cypher) {
            return $this->createToken();
        }

        $token = MinutisToken::unserialize($cypher);

        if ($token->isAccessTokenExpired()) {
            return $this->createToken();
        }

        return $token;
    }

    private function createToken() : MinutisToken
    {
        $response = $this->getClient()->post('/api/auth', [
            'json' => [
                'username' => getenv('MINUTIS_SA_USERNAME'),
                'password' => getenv('MINUTIS_SA_PASSWORD'),
            ],
        ]);

        $payload = json_decode($response->getBody()->getContents(), true);

        $token = new MinutisToken();
        $token->setAccessToken($payload['accessToken']);
        $token->setAccessTokenExpiresAt(time() + $payload['accessTokenTimeout']);

        $this->settingManager->set(Settings::MINUTIS_TOKEN, $token->serialize());

        return $token;
    }

    private function getClient() : Client
    {
        if (null === $this->client) {
            $this->client = new Client([
                'base_uri' => getenv('MINUTIS_URL'),
                'timeout'  => 3,
            ]);
        }

        return $this->client;
    }
}