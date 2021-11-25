<?php

declare(strict_types=1);

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
use Twig\Environment;
use Twig\Error\RuntimeError;

class Modeller implements ModellerInterface
{
    private Reader $reader;

    private HttpClientInterface $client;

    private Environment $twig;

    public function __construct(Reader $reader, HttpClientInterface $client, Environment $twig) {
        $this->reader = $reader;
        $this->client = $client;
        $this->twig = $twig;
    }

    /**
     * @return ArrayCollection|mixed
     */
    public function getData(Repo $repo)
    {
        $annotation = $this->getResource($repo->getModel(), $repo->getIdentifier());
        $response = $this->client->request(
            $annotation->method,
            self::renderEndpoint($annotation, $repo),
            array_merge_recursive($annotation->options, $repo->getOptions())
        );

        $normalizedContent = self::getSerializer()->decode($response->getContent(), $annotation->type);
        $return = new ArrayCollection();
        if (array_values($normalizedContent) === $normalizedContent) {
            foreach ($normalizedContent as $normalizedItem) {
                $return->add(self::getSerializer()->denormalize($normalizedItem, $repo->getModel()));
            }
            return $return;
        }
        return self::getSerializer()->denormalize($normalizedContent, $repo->getModel());
    }

    private function renderEndpoint(Resource $annotation, Repo $repo): string
    {
        try {
            $template = $this->twig->createTemplate($annotation->endpoint);
            $rendered = $this->twig->render($template, $repo->getParameters());
        } catch (RuntimeError $exception) {
            throw $exception->appendMessage(sprintf('Found in model %s', $repo->getModel()));
        }
        return $rendered;
    }

    private static function getSerializer()
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        return new Serializer($normalizers, $encoders);
    }


    private function getResource(string $model, ?string $identifier): Resource
    {
        $reflection = new \ReflectionClass($model);

        $resourceInterface = $this->reader->getClassAnnotation($reflection, ResourceInterface::class);
        if ($resourceInterface instanceof Resources) {
            if (! array_key_exists($identifier, $resourceInterface->resources)) {
                throw new \Exception(sprintf('Identifier: "%s" does not exists in Model: "%s"', $identifier, $model));
            }
            return $resourceInterface->resources[$identifier];
        }
        return $resourceInterface;
    }
}
