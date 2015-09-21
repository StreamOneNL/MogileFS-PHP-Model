<?php
/**
 *
 * Test MogileFS_File_Mapper functions
 * @author Jon Skarpeteig <jon.skarpeteig@gmail.com>
 * @package MogileFS
 * @group MogileFS
 */
class MapperTest extends PHPUnit_Framework_TestCase
{
	protected $_key = 'someKey';
	protected $_testMapper;
	protected $_testFile;
	protected $_resultSet;

	public function setUp()
	{
		$configFile = realpath(dirname(__FILE__) . '/../config.php');
		$config = include $configFile;
		$this->_testMapper = new MogileFS_File_Mapper(array('adapter' => $config['tracker']));

		$this->_testFile = new MogileFS_File(array('key' => $this->_key, 'file' => '/etc/motd', 'md5' => md5_file('/etc/motd')));

		parent::setUp();
	}

	public function testSaveAndDelete()
	{
		$savedFile = $this->_testMapper->save($this->_testFile);
		$this->assertInstanceOf('MogileFS_File', $savedFile);
		$this->assertNotNull($savedFile->getFid());
		$this->assertEquals('default', $savedFile->getClass());
		$this->assertEquals('s1domain', $savedFile->getDomain());
		$this->assertInternalType('array', $savedFile->getPaths());

		$key = $this->_testFile->getKey();
		$this->_testMapper->delete($key);
		$this->assertNull($this->_testMapper->find($key));
	}

	public function testFindAndFetchAll()
	{
		$savedFile = $this->_testMapper->save($this->_testFile);

		$key = $this->_testFile->getKey();

		$file = $this->_testMapper->find($key);
		$this->assertInstanceOf('MogileFS_File', $file);
		$this->assertEquals('s1domain', $file->getDomain());

		$file = clone $this->_testFile;
		$file->setKey('n0suchk3y');
		$this->assertNull($this->_testMapper->find('NoSuchK3y'));
		$this->assertNull($this->_testMapper->findInfo($file));
		$this->assertNull($this->_testMapper->fetchAll(array('NoSuchK3y')));

		$result = $this->_testMapper->fetchAll(array($this->_testFile->getKey()), true);

		$getFile = reset($result);

		// Don't download file - ignore comparison
		$getFile->setFile($savedFile->getFile());

		$this->assertEquals($savedFile, $getFile);
	}

	public function testFileLazyloader()
	{
		$savedFile = $this->_testMapper->save($this->_testFile);

		$this->assertNotNull($savedFile->getClass(true));
		$this->assertNotNull($savedFile->getDomain(true));
		$this->assertNotNull($savedFile->getSize(true));
		$this->assertNotNull($savedFile->getMd5(true));
	}

	public function testFetchFile()
	{
		$this->_testFile->setMapper($this->_testMapper);
		$savedFile = $this->_testMapper->save($this->_testFile);
		$this->assertFileExists($this->_testFile->getFile(true));
		$this->assertGreaterThan(0, filesize($this->_testFile->getFile()));
	}

	public function testGetAdapter()
	{
		$this->assertInstanceOf('MogileFS_File_Mapper_Adapter_Tracker', $this->_testMapper->getAdapter());

		/**
		 * Argument validation test
		 * Expecting MogileFS_Exception with 1XX code
		 */
		$mapper = new MogileFS_File_Mapper();
		try {
			$mapper->getAdapter(); // No adapter set
		} catch (MogileFS_Exception $exc) {
			$this->assertLessThan(200, $exc->getCode(), 'Got unexpected exception code');
			$this->assertGreaterThanOrEqual(100, $exc->getCode(), 'Got unexpected exception code');
			return;
		}
		$this->fail('Did not get MogileFS_Exception exception');
	}

	/**
	 * Argument validation test
	 * Expecting MogileFS_Exception with 1XX code
	 */
	public function testSaveInvalidFile()
	{
		try {
			$this->_testMapper->save(new MogileFS_File()); // Invalid file
		} catch (MogileFS_Exception $exc) {
			$this->assertLessThan(200, $exc->getCode(), 'Got unexpected exception code');
			$this->assertGreaterThanOrEqual(100, $exc->getCode(), 'Got unexpected exception code');
			return;
		}
		$this->fail('Did not get MogileFS_Exception exception');
	}

	/**
	 * Argument validation test
	 * Expecting MogileFS_Exception with 1XX code
	 */
	public function testFetchFileValidation()
	{
		try {
			$this->_testMapper->fetchFile(new MogileFS_File()); // Invalid file
		} catch (MogileFS_Exception $exc) {
			$this->assertLessThan(200, $exc->getCode(), 'Got unexpected exception code');
			$this->assertGreaterThanOrEqual(100, $exc->getCode(), 'Got unexpected exception code');
			return;
		}
		$this->fail('Did not get MogileFS_Exception exception');
	}
}
