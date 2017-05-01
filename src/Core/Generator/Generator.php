<?php

/**
 * @file
 * Contains \WP\Console\Core\Generator\Generator.
 */

namespace WP\Console\Core\Generator;

use WP\Console\Core\Utils\TwigRenderer;
use WP\Console\Core\Utils\FileQueue;

/**
 * Class Generator
 *
 * @package WP\Console\Core\Generator
 */
abstract class Generator
{
    /**
     * @var TwigRenderer
     */
    protected $renderer;

    /**
     * @var FileQueue
     */
    protected $fileQueue;

    /**
     * @param $renderer
     */
    public function setRenderer(TwigRenderer $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * @param $fileQueue
     */
    public function setFileQueue(FileQueue $fileQueue)
    {
        $this->fileQueue = $fileQueue;
    }

    /**
     * @param string $template
     * @param string $target
     * @param array  $parameters
     * @param null   $flag
     *
     * @return bool
     */
    protected function renderFile(
        $template,
        $target,
        $parameters = [],
        $flag = null
    ) {
        if (!is_dir(dirname($target))) {
            mkdir(dirname($target), 0777, true);
        }

        $content = $this->renderer->render($template, $parameters);

        if (file_put_contents($target, $content, $flag)) {
            $this->fileQueue->addFile($target);

            return true;
        }

        return false;
    }
}
