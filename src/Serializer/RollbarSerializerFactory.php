<?php


declare(strict_types=1);

namespace SFErTrack\RollbarSymfonyBundle\Serializer;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\BackedEnumNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class RollbarSerializerFactory
{
    public function __invoke(): Serializer
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [
            new DateTimeNormalizer(),
            new ArrayDenormalizer(),
            new BackedEnumNormalizer(),
            new ObjectNormalizer(),
        ];

        return new Serializer($normalizers, $encoders);
    }
}
