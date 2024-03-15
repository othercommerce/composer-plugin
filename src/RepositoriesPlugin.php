<?php

namespace OtherCommerce\Composer\Repositories;


use Composer\Composer;
use Composer\EventDispatcher\Event;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginEvents;
use Composer\Plugin\PluginInterface;
use Composer\Repository\RepositoryFactory;
use Composer\Repository\RepositoryInterface;
use Composer\Repository\RepositoryManager;


class RepositoriesPlugin implements PluginInterface, EventSubscriberInterface
{
    private const CALLBACK_PRIORITY = 50000;


    private Composer $composer;


    private OtherCommerceRepositoryConfig $config;


    private IOInterface $io;


    private RepositoryManager $manager;


    public static function getSubscribedEvents(): array
    {
        return [
            PluginEvents::INIT => ['onInit', self::CALLBACK_PRIORITY],
        ];
    }


    public function activate(Composer $composer, IOInterface $io): void
    {
        $this->composer = $composer;
        $this->io = $io;
        $this->manager = $composer->getRepositoryManager();
        $this->config = new OtherCommerceRepositoryConfig($composer);
    }


    public function deactivate(Composer $composer, IOInterface $io): void
    {
        // Nothing to do here.
    }


    public function onInit(Event $event): void
    {
        if ($this->config->forceRemoteVersion() || ! ($local = $this->config->getLocalPath())) {
            $this->manager->addRepository($this->createRemoteRepository());
        } else {
            $this->manager->addRepository($this->createLocalRepository($local));
        }
    }


    public function uninstall(Composer $composer, IOInterface $io): void
    {
        // Nothing to do here.
    }


    private function createLocalRepository(string $path): RepositoryInterface
    {
        $this->io->write('<info>Resolving OtherCommerce from local path <options=bold>[' . $path . ']</>...</info>');

        return $this->createRepository(['type' => 'path', 'url' => $path]);
    }


    private function createRemoteRepository(): RepositoryInterface
    {
        $this->io->write('<info>Resolving OtherCommerce from remote repository...</info>');

        return $this->createRepository(['type' => 'git', 'url' => $this->config->getRemoteRepository()]);
    }


    private function createRepository(array $config): RepositoryInterface
    {
        return RepositoryFactory::createRepo($this->io, $this->composer->getConfig(), $config, $this->manager);
    }
}
