<?php


namespace Joinca\ZKUploader\ResourceType;

use Joinca\ZKUploader\ZKUploader;
use Pimple\Container;

class ResourceTypeFactory extends Container
{
    protected $app;
    protected $config;
    protected $backendFactory;
    protected $thumbnailRepository;

    public function __construct(ZKUploader $app)
    {
        parent::__construct();

        $this->app = $app;
        $this->config = $app['config'];
        $this->backendFactory = $app['backend_factory'];
        $this->thumbnailRepository = $app['thumbnail_repository'];
        $this->resizedImageRepository = $app['resized_image_repository'];
    }

    /**
     * Returns the resource type object with a given name.
     *
     * @param string $name resource type name
     *
     * @return ResourceType
     */
    public function getResourceType($name)
    {
        if (!$this->offsetExists($name)) {
            $resourceTypeConfig = $this->config->getResourceTypeNode($name);
            $backend = $this->backendFactory->getBackend($resourceTypeConfig['backend']);

            $this[$name] = new ResourceType($name, $resourceTypeConfig, $backend, $this->thumbnailRepository, $this->resizedImageRepository);
        }

        return $this[$name];
    }
}
