<?php

declare(strict_types=1);

namespace Fabricio872\ApiModeller\Annotations;

use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;

/**
 * @Annotation
 * @NamedArgumentConstructor()
 * @Target({"CLASS", "ANNOTATION"})
 */
final class Resource implements ResourceInterface
{
    /**
     * URL to exact endpoint for the model to receive data from
     *
     * @var string
     */
    public $endpoint;

    /**
     * @Enum({"json", "xml"})
     * @var string
     */
    public $type;

    /**
     * @Enum({"GET", "POST", "PUT", "PATCH", "DELETE"})
     * @var string
     */
    public $method;

    /**
     * Option described on
     *
     * @Ar
     * @var array
     */
    public $options;

    public function __construct(
        string $endpoint = '',
        ?string $type = 'json',
        ?string $method = 'GET',
        ?array $options = []
    ) {
        $this->endpoint = $endpoint;
        $this->type = $type;
        $this->method = $method;
        $this->options = $options;
    }
}
