<?php
/**
 * 
 * Abstract class for MogileFS adapters
 * @author Jon Skarpeteig <jon.skarpeteig@gmail.com>
 * @package MogileFS
 * 
 */
abstract class MogileFS_File_Mapper_Adapter_Abstract
{
	/**
	 * 
	 * Configuration options for adapter
	 * @var array
	 */
	private $options;

	public function __construct(array $options = null)
	{
		if (null !== $options) {
			$this->setOptions($options);
		}
	}

	public function setOptions(array $options)
	{
		$this->options = $options;
		return $this;
	}

	public function getOptions()
	{
		return $this->options;
	}

	/**
	 * 
	 * Looks up paths for key
	 * @param string $key
	 * @return array of string uri paths
	 */
	abstract function findPaths($key);

	/**
	 * 
	 * Look up info such as class and fid
	 * @param unknown_type $key
	 */
	abstract function findInfo($key);

	/**
	 *
	 * Looks up paths for key
	 * @param array $keys
	 * @return array of string uri paths indexed by key
	 */
	abstract function fetchAllPaths(array $keys);

	/**
	 * List keys.
	 *
	 * @param string $prefix OPTIONAL Key prefix
	 * @param string $afterKey OPTIONAL Return keys listed after this key
	 * @param int $limit OPTIONAL Maximum number of keys to return
	 * @return array Array of keys
	 */
	abstract function listKeys($prefix = null, $afterKey = null, $limit = null);
	
	/**
	 * 
	 * Saves file to MogileFS
	 * @param string $key
	 * @param string $file
	 * @param string $md5
	 */
	abstract function saveFile($key, $file, $md5 = null, $class = null);

	/**
	 * 
	 * Renames a key
	 * @param string $fromKey
	 * @param string $toKey
	 */
	abstract function rename($fromKey, $toKey);

	/**
	 * 
	 * Deletes file from MogileFS
	 * @param string $key
	 */
	abstract function delete($key);
}
