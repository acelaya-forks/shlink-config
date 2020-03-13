<?php

declare(strict_types=1);

namespace ShlinkioTest\Shlink\Config;

use PHPUnit\Framework\TestCase;
use Shlinkio\Shlink\Config\ConfigProvider;
use Shlinkio\Shlink\Config\Factory\DottedAccessConfigAbstractFactory;

class ConfigProviderTest extends TestCase
{
    private ConfigProvider $configProvider;

    public function setUp(): void
    {
        $this->configProvider = new ConfigProvider();
    }

    /** @test */
    public function configIsReturned(): void
    {
        $config = ($this->configProvider)();
        $this->assertArrayHasKey('dependencies', $config);
        $this->assertEquals([
            'abstract_factories' => [
                DottedAccessConfigAbstractFactory::class,
            ],
        ], $config['dependencies']);
    }
}
