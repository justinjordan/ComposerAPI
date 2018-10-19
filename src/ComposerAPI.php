<?php

namespace Sxule;

use ReflectionClass;
use Composer\Composer;
use Composer\Factory;
use Composer\Installer;
use Composer\IO\BufferIO;

class ComposerAPI
{
    protected $baseDir;
    protected $composerHome;
    protected $composerFile;

    public function __construct(string $baseDir, string $composerFile = '', string $composerHome = '')
    {
        $this->baseDir = $baseDir;
        $this->composerFile = !empty($composerFile) ? $composerFile : $baseDir . '/composer.json';
        $this->composerHome = !empty($composerHome) ? $composerHome : $baseDir . '/vendor/bin/composer';
        putenv('COMPOSER_HOME=' . $this->composerHome);
    }

    /**
     * Installs all packages in composer.json
     * Same as running `composer install` in terminal
     *
     * @return string   Returns Composer output
     */
    public function install()
    {
        $io = new BufferIO();
        $composer = Factory::create($io, $this->composerFile);
        $installer = Installer::create($io, $composer);

        $response = $installer->run();

        return $io->getOutput();
    }

    /**
     * Updates all packages or those specified to their latest versions
     * Same as running `composer update` in terminal
     *
     * @param   array   Array of packages to update - i.e. ['sxule/meddle']
     *
     * @return  string  Returns Composer output
     */
    public function update(array $packages = [])
    {
        $io = new BufferIO();
        $composer = Factory::create($io, $this->composerFile);
        $installer = Installer::create($io, $composer);

        /** packages to lowercase */
        $packages = array_map('strtolower', $packages);

        $installer
            ->setUpdateWhitelist(!empty($packages) ? $packages : [])
            ->setUpdate(true);

        $response = $installer->run();

        return $io->getOutput();
    }

    /**
     * Gets Composer's absolute vendor path
     * @return string
     */
    public function getVendorDir()
    {
        $ref = new ReflectionClass(Composer::class);
        $path = $ref->getFileName();

        $path = preg_replace('/\/composer\/[\s\S]+\/Composer.php/', '', $path);

        return $path;
    }
}