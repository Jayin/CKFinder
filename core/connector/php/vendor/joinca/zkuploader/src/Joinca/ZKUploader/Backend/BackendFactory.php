<?php



namespace Joinca\ZKUploader\Backend;

use Joinca\ZKUploader\Acl\AclInterface;
use Joinca\ZKUploader\Backend\Adapter\Local as LocalFilesystemAdapter;
use Joinca\ZKUploader\Backend\Adapter\Dropbox as DropboxAdapter;
use Joinca\ZKUploader\ZKUploader;
use Joinca\ZKUploader\Config;
use Joinca\ZKUploader\ContainerAwareInterface;
use Joinca\ZKUploader\Exception\ZKUploaderException;
use Joinca\ZKUploader\Filesystem\Path;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Cached\CachedAdapter;
use League\Flysystem\Cached\CacheInterface;
use League\Flysystem\Adapter\Ftp as FtpAdapter;
use Joinca\ZKUploader\Backend\Adapter\AwsS3 as AwsS3Adapter;
use Joinca\ZKUploader\Backend\Adapter\Azure as AzureAdapter;
use Dropbox\Client as DropboxClient;
use Aws\S3\S3Client;
use League\Flysystem\Cached\Storage\Memory as MemoryCache;
use WindowsAzure\Common\ServicesBuilder;

/**
 * The BackendFactory class.
 *
 * BackendFactory is responsible for the instantiation of backend adapters.
 *
 */
class BackendFactory
{
    /**
     * An array of instantiated backed file systems.
     *
     * @var array
     */
    protected $backends = array();

    /**
     * Registered adapter types.
     *
     * @var array
     */
    protected $registeredAdapters = array();

    /**
     * The list of operations that should be tracked for a given backend type.
     *
     * @var array
     */
    protected static $trackedOperations = array(
        's3' => array('RenameFolder')
    );

    /**
     * The ZKUploader application container.
     *
     * @var ZKUploader $app
     */
    protected $app;

    /**
     * Access Control Lists.
     *
     * @var AclInterface $acl
     */
    protected $acl;

    /**
     * Configuration.
     *
     * @var Config $config
     */
    protected $config;

    /**
     * Constructor.
     *
     * @param ZKUploader $app
     */
    public function __construct(ZKUploader $app)
    {
        $this->app = $app;
        $this->acl = $app['acl'];
        $this->config = $app['config'];

        $this->registerDefaultAdapters();
    }

    protected function registerDefaultAdapters()
    {
        $this->registerAdapter('local', function ($backendConfig) {
            return $this->createBackend($backendConfig, new LocalFilesystemAdapter($backendConfig));
        });

        $this->registerAdapter('ftp', function ($backendConfig) {

            $configurable = array('host', 'port', 'username', 'password', 'ssl', 'timeout', 'root', 'permPrivate', 'permPublic', 'passive');

            $config = array_intersect_key($backendConfig, array_flip($configurable));

            return $this->createBackend($backendConfig, new FtpAdapter($config));
        });

        $this->registerAdapter('dropbox', function ($backendConfig) {

            $client = new DropboxClient($backendConfig['token'], $backendConfig['username']);

            return $this->createBackend($backendConfig, new DropboxAdapter($client, $backendConfig));
        });

        $this->registerAdapter('s3', function ($backendConfig) {
            $clientConfig = array(
                'key'    => $backendConfig['key'],
                'secret' => $backendConfig['secret'],
            );

            if (isset($backendConfig['region'])) {
                $clientConfig['region'] = $backendConfig['region'];
            }

            $client = S3Client::factory($clientConfig);

            $filesystemConfig = array(
                'visibility' => isset($backendConfig['visibility']) ? $backendConfig['visibility'] : 'private'
            );

            $prefix = isset($backendConfig['root']) ? trim($backendConfig['root'], '/ ') : null;

            return $this->createBackend($backendConfig, new AwsS3Adapter($client, $backendConfig['bucket'], $prefix), $filesystemConfig);
        });

        $this->registerAdapter('azure', function ($backendConfig) {
            $endpoint = sprintf('DefaultEndpointsProtocol=https;AccountName=%s;AccountKey=%s', $backendConfig['account'], $backendConfig['key']);
            $blobRestProxy = ServicesBuilder::getInstance()->createBlobService($endpoint);

            $prefix = isset($backendConfig['root']) ? trim($backendConfig['root'], '/ ') : null;

            return $this->createBackend($backendConfig, new AzureAdapter($blobRestProxy, $backendConfig['container'], $prefix));
        });
    }

    /**
     * @param string   $adapterName
     * @param callable $instantiationCallback
     */
    public function registerAdapter($adapterName, callable $instantiationCallback)
    {
        $this->registeredAdapters[$adapterName] = $instantiationCallback;
    }

    /**
     * Creates a backend file system.
     *
     * @param array               $backendConfig
     * @param AdapterInterface    $adapter
     * @param array|null          $filesystemConfig
     * @param CacheInterface|null $cache
     *
     * @return Backend
     */
    public function createBackend(array $backendConfig, AdapterInterface $adapter, array $filesystemConfig = null, CacheInterface $cache = null)
    {
        if ($adapter instanceof ContainerAwareInterface) {
            $adapter->setContainer($this->app);
        }

        if (null === $cache) {
            $cache = new MemoryCache();
        }

        $cachedAdapter = new CachedAdapter($adapter, $cache);

        if (array_key_exists($backendConfig['adapter'], static::$trackedOperations)) {
            $backendConfig['trackedOperations'] = static::$trackedOperations[$backendConfig['adapter']];
        }

        return new Backend($backendConfig, $this->app, $cachedAdapter, $filesystemConfig);
    }

    /**
     * Returns the backend object by name.
     *
     * @param string $backendName
     *
     * @return Backend
     *
     * @throws \InvalidArgumentException
     * @throws ZKUploaderException
     */
    public function getBackend($backendName)
    {
        if (isset($this->backends[$backendName])) {
            return $this->backends[$backendName];
        }

        $backendConfig = $this->config->getBackendNode($backendName);
        $adapterName = $backendConfig['adapter'];

        if (!isset($this->registeredAdapters[$adapterName])) {
            throw new \InvalidArgumentException(sprintf('Backends adapter "%s" not found. Please check configuration file.', $adapterName));
        }

        if (!is_callable($this->registeredAdapters[$adapterName])) {
            throw new \InvalidArgumentException(sprintf('Backend instantiation callback for adapter "%s" is not a callable.', $adapterName));
        }

        $backend = call_user_func($this->registeredAdapters[$adapterName], $backendConfig);

        if (!$backend instanceof Backend) {
            throw new ZKUploaderException(sprintf('The instantiation callback for adapter "%s" didn\'t return a valid Backend object.', $adapterName));
        }

        $this->backends[$backendName] = $backend;

        return $backend;
    }

    /**
     * Returns the backend object for a given private directory identifier.
     *
     * @param string $privateDirIdentifier
     *
     * @return Backend
     */
    public function getPrivateDirBackend($privateDirIdentifier)
    {
        $privateDirConfig = $this->config->get('privateDir');

        if (!array_key_exists($privateDirIdentifier, $privateDirConfig)) {
            throw new \InvalidArgumentException(sprintf('Private dir with identifier %s not found. Please check configuration file.', $privateDirIdentifier));
        }

        $privateDir = $privateDirConfig[$privateDirIdentifier];

        $backend = null;

        if (is_array($privateDir) && array_key_exists('backend', $privateDir)) {
            $backend = $this->getBackend($privateDir['backend']);
        } else {
            $backend = $this->getBackend($privateDirConfig['backend']);
        }

        // Create a default .htaccess to disable access to current private directory
        $privateDirPath = $this->config->getPrivateDirPath($privateDirIdentifier);
        $htaccessPath = Path::combine($privateDirPath, '.htaccess');
        if (!$backend->has($htaccessPath)) {
            $backend->write($htaccessPath, "Order Deny,Allow\nDeny from all\n");
        }

        return $backend;
    }
}
