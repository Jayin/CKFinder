<?php


namespace Joinca\ZKUploader;

/**
 * The ContainerAware interface.
 */
interface ContainerAwareInterface
{
    /**
     * @param ZKUploader $app
     */
    public function setContainer(ZKUploader $app);
}
