<?php


namespace Joinca\ZKUploader\Plugin;

use Joinca\ZKUploader\ZKUploader;

/**
 * The Plugin interface.
 *
 * 
 */
interface PluginInterface
{
    /**
     * Injects the DI container to the plugin.
     *
     * @param ZKUploader $app
     */
    public function setContainer(ZKUploader $app);

    /**
     * Returns an array with the default configuration for this plugin. Any of
     * the plugin configuration options can be overwritten in the ZKUploader configuration file.
     *
     * @return array default plugin configuration
     */
    public function getDefaultConfig();
}
