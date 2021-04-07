<?php

namespace App\Modules\Common\Exception;

use GraphQL\Error\Error;
use GraphQL\Error\FormattedError;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @codeCoverageIgnore
 */
class BaseExceptionNormalizer implements NormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        $exception = $object->getPrevious();
        $error = FormattedError::createFromException($object);
        $error['message'] = $exception->getMessage();
        $error['extensions']['exception_code'] = $exception->getCode();

        return $error;
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof Error && $data->getPrevious() instanceof BaseException;
    }
}
