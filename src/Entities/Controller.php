<?php

namespace Daycry\RestFul\Entities;

use CodeIgniter\Entity\Entity;
use Daycry\RestFul\Models\EndpointModel;

class Controller extends Entity
{
    /**
     * @var Endpoint[]|null
     */
    private ?array $endpoints = null;

    /**
     * @var string[]
     * @phpstan-var list<string>
     * @psalm-var list<string>
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'checked_at'
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'id'          => '?integer',
        'url'         => 'string'
    ];

    protected $castHandlers = [
        'intBol' => \Daycry\RestFul\Entities\Cast\IntBoolCast::class,
    ];

    /**
     * Accessor method for this controller's Endpoint objects.
     * Will populate if they don't exist.
     *
     * @param string $endpoint 'all' returns all endpoints.
     *
     * @return Endpoint[]
     */
    public function getEndpoints(string $endpoint = 'all'): ?array
    {
        $this->populateEndpoints();

        if ($endpoint === 'all') {
            return $this->endpoints;
        }

        $endpoints = [];

        foreach ($this->endpoints as $e) {
            if ($e->method === $endpoint) {
                $endpoints[] = $e;
            }
        }

        return $endpoints;
    }

    /**
     * ensures that all of the controllers's endpoints are loaded
     * into the instance for faster access later.
     */
    private function populateEndpoints(): void
    {
        if ($this->endpoints === null) {
            /** @var EndpointModel $endpointModel */
            $endpointModel = model(EndpointModel::class);

            $this->endpoints = $endpointModel->getEndpoints($this);
        }
    }
}
