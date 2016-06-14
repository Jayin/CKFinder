<?php


namespace Joinca\ZKUploader\Cache\Adapter;

/**
 * The AdapterInterface interface.
 */
interface AdapterInterface
{
    /**
     * Sets the value in cache for a given key.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return bool `true` if successful.
     */
    public function set($key, $value);

    /**
     * Returns the value for a given key from cache.
     *
     * @param string $key
     *
     * @return array
     */
    public function get($key);

    /**
     * Deletes the value for a given key from cache.
     *
     * @param string $key
     *
     * @return bool `true` if successful.
     */
    public function delete($key);

    /**
     * Deletes all cache entries with a given key prefix.
     *
     * @param string $keyPrefix
     */
    public function deleteByPrefix($keyPrefix);

    /**
     * Changes the prefix for all entries given a key prefix.
     *
     * @param string $sourcePrefix
     * @param string $targetPrefix
     */
    public function changePrefix($sourcePrefix, $targetPrefix);
}
