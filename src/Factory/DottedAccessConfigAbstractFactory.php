<?php

declare(strict_types=1);

namespace Shlinkio\Shlink\Config\Factory;

use ArrayAccess;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;
use Shlinkio\Shlink\Config\Exception\InvalidArgumentException;

use function array_key_exists;
use function array_shift;
use function explode;
use function is_array;
use function sprintf;
use function substr_count;

class DottedAccessConfigAbstractFactory implements AbstractFactoryInterface
{
    public function canCreate(ContainerInterface $container, $requestedName): bool // phpcs:ignore
    {
        return substr_count($requestedName, '.') > 0;
    }

    /**
     * Create an object
     *
     * @param string $requestedName
     * @return mixed|null
     */
    // phpcs:ignore
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        $parts = explode('.', $requestedName);
        $serviceName = array_shift($parts);
        if (! $container->has($serviceName)) {
            throw new ServiceNotCreatedException(sprintf(
                'Defined service "%s" could not be found in container after resolving dotted expression "%s".',
                $serviceName,
                $requestedName,
            ));
        }

        $array = $container->get($serviceName);
        return $this->readKeysFromArray($parts, $array);
    }

    /**
     * @param array $keys
     * @param array|\ArrayAccess $array
     * @return mixed|null
     * @throws  InvalidArgumentException
     */
    private function readKeysFromArray(array $keys, $array)
    {
        $key = array_shift($keys);

        // As soon as one of the provided keys is not found, throw an exception
        if (! array_key_exists($key, $array)) {
            throw new InvalidArgumentException(sprintf(
                'The key "%s" provided in the dotted notation could not be found in the array service',
                $key,
            ));
        }

        $value = $array[$key];
        if (! empty($keys) && (is_array($value) || $value instanceof ArrayAccess)) {
            $value = $this->readKeysFromArray($keys, $value);
        }

        return $value;
    }
}
