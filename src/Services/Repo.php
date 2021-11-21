<?php

namespace Fabricio872\ApiModeller\Services;

class Repo
{
    private string $model;
    private ?string $identifier = '';
    private array $parameters = [];

    public function __construct(
        string $model
    )
    {
        $this->model = $model;
    }

    public static function new(string $model)
    {
        return new self($model);
    }

    /**
     * @return string
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * @return string|null
     */
    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    /**
     * @param string|null $identifier
     * @return Repo
     */
    public function setIdentifier(?string $identifier): Repo
    {
        $this->identifier = $identifier;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getParameters(): ?array
    {
        return $this->parameters;
    }

    /**
     * @param array|null $parameters
     * @return Repo
     */
    public function setParameters(?array $parameters): Repo
    {
        $this->parameters = $parameters;
        return $this;
    }
}