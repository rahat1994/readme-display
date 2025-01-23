<?php

namespace ReadmeDisplay\Dev;

use Composer\Script\Event;
use InvalidArgumentException;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class ComposerScript
{
    private static $composerUpdateReport = __DIR__.'/composer-update-report.json';

    private static $packagePostInstallOrUpdate = [];

    public static function run(Event $event)
    {
        static::runComposerScript($event);
        shell_exec('composer dump-autoload');
    }

    private static function runComposerScript(Event $event)
    {
        echo "Please wait, this may take a while." . PHP_EOL;

        static::createOrUpdateComposerReportFile();
        
        $namespace = static::getNamespaceFromComposerJson(
            $vendorDir = $event->getComposer()->getConfig()->get('vendor-dir')
        );

        // Only update namespace in wpfluent package (all files)
        static::updateNamespaceInWPFluentPackage($vendorDir, $namespace);

        // Update other composer packages and update installed.json psr-4
        static::updateVendorComposerFiles($vendorDir, $namespace, $event);

        // Update autoload files and update installed.json autoload-files
        static::updateComposerAutoloadFilesEntries($vendorDir, $namespace);
    }

    private static function createOrUpdateComposerReportFile()
    {
        if (!file_exists(static::$composerUpdateReport)) {
            touch(static::$composerUpdateReport);
        } else {
            file_put_contents(static::$composerUpdateReport, '');
        }
    }

    private static function getNamespaceFromComposerJson($vendorDir)
    {
        $composerJsonPath = $vendorDir . '/../composer.json';

        if (!file_exists($composerJsonPath)) {
            throw new InvalidArgumentException("composer.json not found.");
        }

        $namespace = json_decode(
            file_get_contents($composerJsonPath)
        )->extra->wpfluent->namespace->current ?? null;

        if (!$namespace) {
            throw new InvalidArgumentException(
                "Namespace not set in composer.json."
            );
        }

        return $namespace;
    }

    static function updateNamespaceInWPFluentPackage($vendorDir, $namespace)
    {
        $iterator = static::createIterator(
            $vendorDir . '/wpfluent/framework/src/'
        );

        foreach ($iterator as $file) {
            if ($file->isDir()) { 
                continue;
            }

            $fileName = $file->getPathname();

            $content = file_get_contents($fileName);

            $content = str_replace(
                ['WPFluent\\', 'WPFluentPackage\\'],
                [$namespace . '\\Framework\\', $namespace . '\\'],
                $content
            );

            file_put_contents($fileName, $content);
        }
    }

    private static function updateVendorComposerFiles(
        $vendorDir, $namespace, $event
    )
    {
        $composerInstalledJson = json_decode(file_get_contents(
            $installedJsonFile = $vendorDir . '/composer/installed.json'
        ), true);

        foreach ($composerInstalledJson['packages'] as &$package) {
            try {
                if ($package['name'] == 'wpfluent/framework') {
                    $package['autoload']['psr-4'] = [
                        $namespace . "\\Framework\\" => "src/WPFluent"
                    ];
                } else {
                    $packageDir = $vendorDir . "/{$package['name']}/src/";

                    // Only for any composer package (not wpfluent package)
                    if (!str_contains($package['name'], 'wpfluent')) {
                        
                        // Replace the namespace using namespace replacer script
                        static::updateComposerPackageNamespace(
                            str_replace('src/', '', $packageDir), $namespace, $event
                        );

                        if (!isset($package['autoload']['psr-4'])) continue;
                        
                        foreach ($package['autoload']['psr-4'] as $key => $value) {
                            $package['autoload']['psr-4'][$namespace.'\\'.$key] = $value;
                        }

                        foreach ($package['autoload']['psr-4'] as $key => $value) {
                            if (!str_contains($key, $namespace)) {
                                unset($package['autoload']['psr-4'][$key]);
                            }
                        }

                        $packageComposerJson = json_decode(file_get_contents(
                            $vendorDir .'/' . $package['name'] . '/composer.json'
                        ), true);
                        
                        foreach (
                            $packageComposerJson['autoload']['psr-4'] as $k => $v
                        ) {
                            $package['autoload']['psr-4'][$namespace.'\\'.$k] = $v;
                        }

                        file_put_contents(
                            $vendorDir .'/' . $package['name'] . '/composer.json',
                            json_encode(
                                $packageComposerJson,
                                JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES
                            )
                        );
                    } else {
                        // For wpfluent packages (not a general composer package)
                        $iterator = static::createIterator($packageDir);

                        foreach ($iterator as $item) {

                            if ($item->isDir()) {
                                continue;
                            }

                            $fileName = $item->getPathname();
                            $content = file_get_contents($fileName);

                            // Replace Test and WPFluentPackage/WpfluentPackage
                            $content = str_replace(
                                [
                                    'Test\\',
                                    'WPFluent\\',
                                    'WPFluentPackage\\',
                                    'WpfluentPackage\\'
                                ],
                                [
                                    $namespace . '\\Framework\\',
                                    $namespace . '\\Framework\\',
                                    $namespace . '\\',
                                    $namespace . '\\'
                                ],
                                $content
                            );

                            file_put_contents($fileName, $content);
                        }

                        $psr4 = array_keys($package['autoload']['psr-4']);
                        
                        $replaced = str_replace(
                            ['WPFluentPackage', 'WpfluentPackage'],
                            [$namespace, $namespace],
                            $psr4[0]
                        );
                        
                        $package['autoload']['psr-4'] = [
                            $replaced => "src/"
                        ];

                        $packageComposerJson = json_decode(file_get_contents(
                            $vendorDir .'/' . $package['name'] . '/composer.json'
                        ), true);
                        
                        $packageComposerJson['autoload']['psr-4'] = [
                            $replaced => "src/"
                        ];

                        file_put_contents(
                            $vendorDir .'/' . $package['name'] . '/composer.json',
                            json_encode($packageComposerJson, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES)
                        );

                        static::$packagePostInstallOrUpdate[] = [
                            $vendorDir, $package, $event
                        ];
                    }
                }
            } catch (\Exception $e) {
                echo $e->getMessage() . PHP_EOL;
                continue;
            }
        }

        file_put_contents(
            $installedJsonFile,
            json_encode(
                $composerInstalledJson,
                JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT
            )
        );
    }

    private static function updateComposerAutoloadFilesEntries(
        $vendorDir, $namespace
    )
    {
        $composerReportData = (array) json_decode(
            file_get_contents(static::$composerUpdateReport), true
        );

        $installedJson = json_decode(file_get_contents(
            $installedJsonFile = $vendorDir . '/composer/installed.json'
        ), true);

        foreach ($composerReportData as $composerFile => $file) {

            $newName = static::renameFile($file['path'], $namespace);
            
            $composerFileData = json_decode(
                file_get_contents($composerFile), true
            );
            
            if (isset($composerFileData['autoload']['files'])) {
                
                $files = $composerFileData['autoload']['files'];
                
                foreach ($files as $key => $fileName) {
                    
                    if ($file['name'] === $fileName) {
                        $ds = DIRECTORY_SEPARATOR;
                        $arr = explode($ds, $file['name']);
                        
                        if (count($arr) > 1) {
                            array_pop($arr);
                            $pathParts = implode($ds, $arr);
                            $pathParts = rtrim($pathParts, $ds).$ds;
                            $files[$key] =  $pathParts.pathinfo($newName)['basename'];
                        } else {
                            $files[$key] = pathinfo($newName)['basename'];
                        }

                        foreach (
                            $installedJson['packages'] as $key => &$packageData
                        ) {
                            if ($packageData['name'] === $file['package']) {
                                $packageData['autoload']['files'] = $files;
                            }
                        }
                    }
                }

                $composerFileData['autoload']['files'] = $files;
            }
            
            file_put_contents($composerFile, json_encode(
                $composerFileData, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES
            ));

            file_put_contents($installedJsonFile, json_encode(
                $installedJson, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES
            ));
        }
    }

    private static function updateComposerPackageNamespace(
        $inDir, $namespace, $event
    )
    {
        $composerUpdateReport = static::$composerUpdateReport;

        if (file_exists($inDir)) {
            (require __DIR__.'/cli/namespace_fixer.php')($inDir, $namespace);
        }
    }

    private static function renameFile($path, $namespace)
    {
        $nameParts = explode('/', $path);

        $name = array_pop($nameParts);
        
        if (str_starts_with($name, $namespace)) {
            return $path;
        }

        $dirPath = implode('/', $nameParts);

        $newName = $dirPath . '/' . $namespace . '-' . md5($path) . '-' . $name;
        
        if (rename($path, $newName)) {
            return $newName;
        }
    }

    private static function createIterator(string $directory)
    {
        if (!is_dir($directory)) {
            throw new InvalidArgumentException(
                "The directory '{$directory}' does not exist."
            );
        }

        return new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $directory, RecursiveDirectoryIterator::SKIP_DOTS
            ), RecursiveIteratorIterator::SELF_FIRST
        );
    }
}
