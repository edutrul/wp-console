<?php

/**
 * @file
 * Contains \WP\Console\Generator\PluginGenerator.
 */

namespace WP\Console\Generator;

use WP\Console\Core\Generator\Generator;

/**
 * Class PluginGenerator
 *
 * @package WP\Console\Generator
 */
class PluginGenerator extends Generator
{
    /**
     * @param $module
     * @param $machineName
     * @param $dir
     * @param $description
     * @param $core
     * @param $package
     * @param $moduleFile
     * @param $featuresBundle
     * @param $composer
     * @param $dependencies
     * @param $test
     * @param $twigtemplate
     */
    public function generate(
        $plugin,
        $machineName,
        $dir,
        $description,
        $core,
        $package,
        $pluginFile,
        $featuresBundle,
        $composer,
        $dependencies,
        $test,
        $twigtemplate
    ) {
        $dir .= '/'.$machineName;
        if (file_exists($dir)) {
            if (!is_dir($dir)) {
                throw new \RuntimeException(
                    sprintf(
                        'Unable to generate the plugin as the target directory "%s" exists but is a file.',
                        realpath($dir)
                    )
                );
            }
            $files = scandir($dir);
            if ($files != ['.', '..']) {
                throw new \RuntimeException(
                    sprintf(
                        'Unable to generate the module as the target directory "%s" is not empty.',
                        realpath($dir)
                    )
                );
            }
            if (!is_writable($dir)) {
                throw new \RuntimeException(
                    sprintf(
                        'Unable to generate the module as the target directory "%s" is not writable.',
                        realpath($dir)
                    )
                );
            }
        }

        $parameters = [
            'plugin' => $plugin,
            'machine_name' => $machineName,
            'type' => 'module',
            'core' => $core,
            'description' => $description,
            'package' => $package,
            'dependencies' => $dependencies,
            'test' => $test,
            'twigtemplate' => $twigtemplate,
        ];

        $this->renderFile(
            'plugin/info.yml.twig',
            $dir.'/'.$machineName.'.info.yml',
            $parameters
        );

        if (!empty($featuresBundle)) {
            $this->renderFile(
                'plugin/features.yml.twig',
                $dir.'/'.$machineName.'.features.yml',
                [
                    'bundle' => $featuresBundle,
                ]
            );
        }

        if ($pluginFile) {
            $this->renderFile(
                'plugin/module.twig',
                $dir . '/' . $machineName . '.module',
                $parameters
            );
        }

        if ($composer) {
            $this->renderFile(
                'plugin/composer.json.twig',
                $dir.'/'.'composer.json',
                $parameters
            );
        }

        if ($test) {
            $this->renderFile(
                'plugin/src/Tests/load-test.php.twig',
                $dir . '/src/Tests/' . 'LoadTest.php',
                $parameters
            );
        }
        if ($twigtemplate) {
            $this->renderFile(
                'plugin/module-twig-template-append.twig',
                $dir .'/' . $machineName . '.module',
                $parameters,
                FILE_APPEND
            );
            $dir .= '/templates/';
            if (file_exists($dir)) {
                if (!is_dir($dir)) {
                    throw new \RuntimeException(
                        sprintf(
                            'Unable to generate the templates directory as the target directory "%s" exists but is a file.',
                            realpath($dir)
                        )
                    );
                }
                $files = scandir($dir);
                if ($files != ['.', '..']) {
                    throw new \RuntimeException(
                        sprintf(
                            'Unable to generate the templates directory as the target directory "%s" is not empty.',
                            realpath($dir)
                        )
                    );
                }
                if (!is_writable($dir)) {
                    throw new \RuntimeException(
                        sprintf(
                            'Unable to generate the templates directory as the target directory "%s" is not writable.',
                            realpath($dir)
                        )
                    );
                }
            }
            $this->renderFile(
                'plugin/twig-template-file.twig',
                $dir . $machineName . '.html.twig',
                $parameters
            );
        }
    }
}
