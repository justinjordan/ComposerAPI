<?php

namespace sxule;

use Composer\Factory;
use Composer\Installer;
use Composer\IO\BufferIO;

/**
 * Integrates with Composer to automatically downloads LOL's Composer packages
 * into the project and to allow future plugin installation via Composer
 */
class ComposerAPI
{
    protected $baseDir;
    protected $composerHome;
    protected $composerFile;

    public function __construct(string $baseDir, string $composerHome = '', string $composerFile = '')
    {
        $this->baseDir = $baseDir;
        $this->composerHome = !empty($composerHome) ? $composerHome : $baseDir . '/vendor/bin/composer';
        $this->composerFile = !empty($composerFile) ? $composerFile : $baseDir . '/composer.json';
    }

    /**
     * Same as running `composer update` in terminal
     *
     * Updates all packages or those specified to their latest versions.
     *
     * @param   array   Array of packages to update... ['sxule/meddle']
     *
     * @return  string  Returns Composer output
     */
    public function update(array $packages = [])
    {
        putenv('COMPOSER_HOME=' . $this->composerHome);

        $io = new BufferIO();
        $composer = Factory::create($io, $this->composerFile);
        $installer = Installer::create($io, $composer);

        /** packages to lower */
        $packages = array_map('strtolower', $packages);

        $installer
            ->setUpdateWhitelist(!empty($packages) ? $packages : [])
            ->setUpdate(true);

        $installer->run();

        return $io->getOutput();
    }
}