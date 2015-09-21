<?php
/**
 *
 * Test adapter for MogileFS
 * @author Jon Skarpeteig <jon.skarpeteig@gmail.com>
 * @package MogileFS
 *
 */
class MogileFS_File_Mapper_Adapter_Test extends MogileFS_File_Mapper_Adapter_Abstract
{
	protected $_saveResult = array();

	public function findPaths($key)
	{
		return isset($this->_saveResult[$key]['paths']) ? $this->_saveResult[$key]['paths'] : null;
	}

	public function findInfo($key)
	{
		return isset($this->_saveResult[$key]) ? $this->_saveResult[$key] : null;
	}

	public function fetchAllPaths(array $keys)
	{
		$result = array();
		foreach ($keys as $key) {
			$paths = $this->findPaths($key);
			if (null !== $paths) {
				$result[$key] = $this->findPaths($key);
			}
		}
		return $result;
	}

	public function listKeys($prefix = null, $afterKey = null, $limit = null)
	{
		if (null !== $limit && !is_numeric($limit)) {
			throw new InvalidArgumentException(
					__METHOD__ . ' Expected limit argument to be numeric or null. Got: ' . gettype($limit));
		}

		$keys = array_keys($this->_saveResult);
		sort($keys);

		$realLimit = (null === $limit) ? INF : $limit;
		$keysToReturn = array();
		$afterKeyHit = (null === $afterKey) ? true : false;
		foreach ($keys as $key) {
			// Filter keys
			if ($afterKeyHit && (null === $prefix || 0 === strpos($key, $prefix))) {
				$keysToReturn[] = $key;
			}
			
			if ($key == $afterKey) {
				$afterKeyHit = true;
			}

			// Limit reached
			if (count($keysToReturn) == $realLimit) {
				return $keysToReturn;
			}
		}
		
		return $keysToReturn;
	}

	public function saveFile($key, $file, $md5 = null, $class = null)
	{
		$options = $this->getOptions();
		if (!isset($options['domain'])) {
			throw new MogileFS_Exception(__METHOD__ . ' Mandatory option \'domain\' missing from options',
					MogileFS_Exception::MISSING_OPTION);
		}

		$fid = rand(0, 1000);
		$this->_saveResult[$key] = array('fid' => $fid, 'key' => $key, 'size' => 123, 'md5' => $md5,
				'paths' => array('file://' . $file), 'domain' => $options['domain'],
				'class' => (null === $class) ? 'default' : $class
		);

		return $this->_saveResult[$key];
	}

	public function rename($fromKey, $toKey)
	{
		if (isset($this->_saveResult[$fromKey])) {
			$this->_saveResult[$toKey] = $this->_saveResult[$fromKey];
			unset($this->_saveResult[$fromKey]);
		}
		return;
	}

	public function delete($key)
	{
		if (isset($this->_saveResult[$key])) {
			unset($this->_saveResult[$key]);
		}
		return;
	}
}
