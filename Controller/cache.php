<?php
// lib/cache.php

/**
 * Simple file-based caching class.
 * Uses a dedicated directory for cache files.
 */
class SimpleCache
{
    private $cacheDir;
    private $defaultTTL; // Time To Live in seconds

    public function __construct($defaultTTL = 3600) // Default 1 hour
    {
        $this->cacheDir = __DIR__ . '/../Cache/';
        $this->defaultTTL = $defaultTTL;

        if (!is_dir($this->cacheDir)) {
            @mkdir($this->cacheDir, 0777, true);
        }
    }

    private function getCacheFilePath($key)
    {
        // Sanitize key to be a valid filename
        $filename = preg_replace('/[^a-zA-Z0-9_]/', '_', $key);
        return $this->cacheDir . $filename . '.cache';
    }

    /**
     * Stores data in the cache.
     *
     * @param string $key The key under which to store the data.
     * @param mixed $data The data to store.
     * @param int|null $ttl Time to live in seconds. If null, uses defaultTTL.
     * @return bool True on success, false on failure.
     */
    public function set($key, $data, $ttl = null)
    {
        $ttl = $ttl ?? $this->defaultTTL;
        $filePath = $this->getCacheFilePath($key);
        $expiry = time() + $ttl;
        $content = serialize(['expiry' => $expiry, 'data' => $data]);

        return file_put_contents($filePath, $content, LOCK_EX) !== false;
    }

    /**
     * Retrieves data from the cache.
     *
     * @param string $key The key of the data to retrieve.
     * @return mixed The cached data, or false if the key does not exist or has expired.
     */
    public function get($key)
    {
        $filePath = $this->getCacheFilePath($key);

        if (!is_file($filePath)) {
            return false;
        }

        $content = file_get_contents($filePath);
        if ($content === false) {
            return false;
        }

        $cached = @unserialize($content);

        if ($cached === false || !isset($cached['expiry']) || !isset($cached['data'])) {
            // Corrupted cache file
            @unlink($filePath);
            return false;
        }

        if ($cached['expiry'] < time()) {
            // Cache expired
            @unlink($filePath);
            return false;
        }

        return $cached['data'];
    }

    /**
     * Deletes a key from the cache.
     *
     * @param string $key The key to delete.
     * @return bool True on success, false on failure.
     */
    public function delete($key)
    {
        $filePath = $this->getCacheFilePath($key);
        if (is_file($filePath)) {
            return @unlink($filePath);
        }
        return true;
    }
}