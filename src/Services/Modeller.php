<?php

namespace Fabricio872\ApiModeller\Services;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Collections\ArrayCollection;
use Fabricio872\ApiModeller\Annotations\Resource;
use Fabricio872\ApiModeller\Annotations\ResourceInterface;
use Fabricio872\ApiModeller\Annotations\Resources;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Modeller implements ModellerInterface
{
    private Reader $reader;
    private HttpClientInterface $client;

    public function __construct(
        Reader              $reader,
        HttpClientInterface $client
    ) {
        $this->reader = $reader;
        $this->client = $client;
    }

    /**
     * @param string $model
     * @return ArrayCollection|mixed
     * @throws \ReflectionException
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function getData(string $model, ?string $identifier = null)
    {
        $annotation = $this->getResource($model, $identifier);
        $response = $this->client->request($annotation->method, $annotation->endpoint, $annotation->options);

        $normalizedContent = $this->getSerializer()->decode($response->getContent(), $annotation->type);
        $return = new ArrayCollection();
        if (array_values($normalizedContent) === $normalizedContent) {
            foreach ($normalizedContent as $normalizedItem) {
                $return->add($this->getSerializer()->denormalize($normalizedItem, $model));
            }
            return $return;
        } else {
            return $this->getSerializer()->denormalize($normalizedContent, $model);
        }
    }

    private function getSerializer()
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        return new Serializer($normalizers, $encoders);
    }

    /**
     * @param string $model
     * @return ?Resource
     * @throws \ReflectionException
     */
    private function getResource(string $model, ?string $identifier): ?Resource
    {
        $reflection = new \ReflectionClass($model);

        $resourceInterface = $this->reader->getClassAnnotation($reflection, ResourceInterface::class);
        if ($resourceInterface instanceof Resources) {
            if (!array_key_exists($identifier, $resourceInterface->resources)) {
                throw new \Exception(sprintf('Identifier: "%s" does not exists in Model: "%s"', $identifier, $model));
            }
            return $resourceInterface->resources[$identifier];
        }
        return $resourceInterface;
    }
}
