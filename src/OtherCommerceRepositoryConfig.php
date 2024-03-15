<?php

namespace OtherCommerce\Composer\Repositories;


use Composer\Composer;


class OtherCommerceRepositoryConfig
{
    private const ENV_LOCAL_PATH = 'OTHER_COMMERCE_PATH';


    private Composer $composer;


    private array $config;


    public function __construct(Composer $composer)
    {
        $this->composer = $composer;

        $this->config = $this->merge([
            $this->configDefaults(),
            $this->configFromFile(),
        ]);
    }


    public function forceRemoteVersion(): bool
    {
        return (bool) $this->config['remote']['force'];
    }


    public function getLocalPath(): string|false
    {
        return $this->config['local']['path'];
    }


    public function getRemoteRepository(): string
    {
        return $this->config['remote']['url'];
    }


    private function configDefaults(): array
    {
        return [
            'remote' => [
                'url' => 'git@gitlab.promoznawcy.pl:procommerce/monorepo.git',
                'force' => false,
            ],
            'local' => [
                'path' => $this->getDefaultLocalPath(),
            ],
        ];
    }


    private function configFromFile(): array
    {
        if ($path = $this->getConfigFilePath()) {
            return json_decode(file_get_contents($path), true);
        }

        return [];
    }


    private function getConfigFilePath(): string
    {
        $supported = ['oc.json', 'othercommerce.json', 'other_commerce.json'];

        foreach ($supported as $file) {
            if ($path = realpath($this->vendors() . '/../' . $file)) {
                return $path;
            }
        }

        return false;
    }


    private function getDefaultLocalPath(): string|false
    {
        $path = getenv(self::ENV_LOCAL_PATH);

        if (is_string($path)) {
            return $path;
        }

        return false;
    }


    private function merge(array $arrays, $preserve_integer_keys = false): array
    {
        $result = [];

        foreach ($arrays as $array) {
            foreach ($array as $key => $value) {
                if (is_int($key) && ! $preserve_integer_keys) {
                    $result[] = $value;
                } elseif (isset($result[$key]) && is_array($result[$key]) && is_array($value)) {
                    $result[$key] = $this->merge([$result[$key], $value], $preserve_integer_keys);
                } else {
                    $result[$key] = $value;
                }
            }
        }

        return $result;
    }


    private function vendors(): string
    {
        return $this->composer->getConfig()->get('vendor-dir');
    }
}
