<?php



namespace Joinca\ZKUploader\Backend\Adapter;

use Dropbox\Client;

/**
 * The Dropbox class.
 *
 * Extends the default Dropbox adapter to add some extra features.
 */
class Dropbox extends \League\Flysystem\Dropbox\DropboxAdapter
{
    /**
     * Backend configuration node.
     *
     * @var array $backendConfig
     */
    protected $backendConfig;

    /**
     * Constructor.
     *
     * @param Client $client
     * @param array  $backendConfig
     */
    public function __construct(Client $client, array $backendConfig)
    {
        $this->backendConfig = $backendConfig;

        parent::__construct($client, isset($backendConfig['root']) ? $backendConfig['root'] : null);
    }

    /**
     * Returns a direct link to a file stored in Dropbox.
     *
     * @param string $path
     *
     * @return string
     */
    public function getFileUrl($path)
    {
        $shareableLink = $this->client->createShareableLink($this->applyPathPrefix($path));

        if (substr($shareableLink, -5) === '?dl=0') {
            $shareableLink[strlen($shareableLink)-1] = '1';
        }

        return $shareableLink;
    }
}
