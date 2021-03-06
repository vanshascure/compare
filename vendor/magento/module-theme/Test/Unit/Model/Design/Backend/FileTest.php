<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Theme\Test\Unit\Model\Design\Backend;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\File\Mime;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\Filesystem\Io\File as IoFileSystem;
use Magento\Framework\UrlInterface;
use Magento\MediaStorage\Helper\File\Storage\Database;
use Magento\Theme\Model\Design\Backend\File;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class FileTest extends \PHPUnit\Framework\TestCase
{
    /** @var WriteInterface|PHPUnit_Framework_MockObject_MockObject */
    private $mediaDirectory;

    /** @var UrlInterface|PHPUnit_Framework_MockObject_MockObject */
    private $urlBuilder;

    /** @var File */
    private $fileBackend;

    /** @var IoFileSystem|PHPUnit_Framework_MockObject_MockObject */
    private $ioFileSystem;

    /**
     * @var Mime|PHPUnit_Framework_MockObject_MockObject
     */
    private $mime;

    /**
     * @var Database|PHPUnit_Framework_MockObject_MockObject
     */
    private $databaseHelper;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $context = $this->getMockObject(\Magento\Framework\Model\Context::class);
        $registry = $this->getMockObject(\Magento\Framework\Registry::class);
        $config = $this->getMockObjectForAbstractClass(
            \Magento\Framework\App\Config\ScopeConfigInterface::class
        );
        $cacheTypeList = $this->getMockObjectForAbstractClass(
            \Magento\Framework\App\Cache\TypeListInterface::class
        );
        $uploaderFactory = $this->getMockObject(
            \Magento\MediaStorage\Model\File\UploaderFactory::class,
            ['create']
        );
        $requestData = $this->getMockObjectForAbstractClass(
            \Magento\Config\Model\Config\Backend\File\RequestData\RequestDataInterface::class
        );
        $filesystem = $this->getMockBuilder(\Magento\Framework\Filesystem::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->mediaDirectory = $this->getMockBuilder(
            WriteInterface::class
        )
            ->getMockForAbstractClass();
        $filesystem->expects($this->once())
            ->method('getDirectoryWrite')
            ->with(DirectoryList::MEDIA)
            ->willReturn($this->mediaDirectory);
        $this->urlBuilder = $this->getMockBuilder(\Magento\Framework\UrlInterface::class)
            ->getMockForAbstractClass();
        $this->ioFileSystem = $this->getMockBuilder(\Magento\Framework\Filesystem\Io\File::class)
            ->getMockForAbstractClass();
        $this->mime = $this->getMockBuilder(Mime::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->databaseHelper = $this->getMockBuilder(
            Database::class
        )
            ->disableOriginalConstructor()
            ->getMock();
        $abstractResource = $this->getMockBuilder(
            \Magento\Framework\Model\ResourceModel\AbstractResource::class
        )
            ->getMockForAbstractClass();
        $abstractDb = $this->getMockBuilder(\Magento\Framework\Data\Collection\AbstractDb::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->fileBackend = new File(
            $context,
            $registry,
            $config,
            $cacheTypeList,
            $uploaderFactory,
            $requestData,
            $filesystem,
            $this->urlBuilder,
            $abstractResource,
            $abstractDb,
            [],
            $this->databaseHelper,
            $this->ioFileSystem
        );
        $objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $objectManager->setBackwardCompatibleProperty(
            $this->fileBackend,
            'mime',
            $this->mime
        );
    }

    /**
     * @inheritdoc
     */
    public function tearDown()
    {
        unset($this->fileBackend);
    }

    /**
     * Gets the mock object.
     *
     * @param string $class
     * @param array $methods
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function getMockObject(string $class, array $methods = []): PHPUnit_Framework_MockObject_MockObject
    {
        $builder =  $this->getMockBuilder($class)
            ->disableOriginalConstructor();
        if (count($methods)) {
            $builder->setMethods($methods);
        }
        return  $builder->getMock();
    }

    /**
     * Gets mock objects for abstract class.
     *
     * @param string $class
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function getMockObjectForAbstractClass(string $class): PHPUnit_Framework_MockObject_MockObject
    {
        return  $this->getMockBuilder($class)
            ->getMockForAbstractClass();
    }

    /**
     * Test for afterLoad method.
     */
    public function testAfterLoad()
    {
        $value = 'filename.jpg';
        $mime = 'image/jpg';

        $absoluteFilePath = '/absolute_path/' . $value;

        $this->fileBackend->setData(
            [
                'value' => $value,
                'field_config' => [
                    'upload_dir' => [
                        'value' => 'value',
                        'config' => 'system/filesystem/media',
                    ],
                    'base_url' => [
                        'type' => 'media',
                        'value' => 'design/file'
                    ],
                ],
            ]
        );

        $this->mediaDirectory->expects($this->once())
            ->method('isExist')
            ->with('value/' . $value)
            ->willReturn(true);
        $this->mediaDirectory->expects($this->once())
            ->method('getAbsolutePath')
            ->with('value/' . $value)
            ->willReturn($absoluteFilePath);
        $this->urlBuilder->expects($this->once())
            ->method('getBaseUrl')
            ->with(['_type' => UrlInterface::URL_TYPE_MEDIA])
            ->willReturn('http://magento2.com/pub/media/');
        $this->mediaDirectory->expects($this->once())
            ->method('getRelativePath')
            ->with('value')
            ->willReturn('value');
        $this->mediaDirectory->expects($this->once())
            ->method('stat')
            ->with('value/' . $value)
            ->willReturn(['size' => 234234]);
        $this->mime->expects($this->once())
            ->method('getMimeType')
            ->with($absoluteFilePath)
            ->willReturn($mime);
        $this->fileBackend->afterLoad();
        $this->assertEquals(
            [
                [
                    'url' => 'http://magento2.com/pub/media/design/file/' . $value,
                    'file' => $value,
                    'size' => 234234,
                    'exists' => true,
                    'name' => $value,
                    'type' => $mime,
                ]
            ],
            $this->fileBackend->getValue()
        );
    }

    /**
     * Test for beforeSave method.
     *
     * @dataProvider beforeSaveDataProvider
     * @param string $fileName
     * @throws LocalizedException
     */
    public function testBeforeSave(string $fileName)
    {
        $expectedFileName = basename($fileName);
        $expectedTmpMediaPath = 'tmp/design/file/' . $expectedFileName;
        $this->fileBackend->setData(
            [
                'scope' => 'store',
                'scope_id' => 1,
                'value' => [
                    [
                        'url' => 'http://magento2.com/pub/media/tmp/image/' . $fileName,
                        'file' => $fileName,
                        'size' => 234234,
                    ]
                ],
                'field_config' => [
                    'upload_dir' => [
                        'value' => 'value',
                        'config' => 'system/filesystem/media',
                    ],
                ],
            ]
        );

        $this->databaseHelper->expects($this->once())
            ->method('renameFile')
            ->with($expectedTmpMediaPath, '/' . $expectedFileName)
            ->willReturn(true);

        $this->mediaDirectory->expects($this->once())
            ->method('copyFile')
            ->with($expectedTmpMediaPath, '/' . $expectedFileName)
            ->willReturn(true);
        $this->mediaDirectory->expects($this->once())
            ->method('delete')
            ->with($expectedTmpMediaPath);

        $this->fileBackend->beforeSave();
        $this->assertEquals($expectedFileName, $this->fileBackend->getValue());
    }

    /**
     * Data provider for testBeforeSave.
     *
     * @return array
     */
    public function beforeSaveDataProvider(): array
    {
        return [
            'Normal file name' => ['filename.jpg'],
            'Vulnerable file name' => ['../../../../../../../../etc/pass'],
        ];
    }

    /**
     * Test for beforeSave method without file.
     *
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage header_logo_src does not contain field 'file'
     */
    public function testBeforeSaveWithoutFile()
    {
        $this->fileBackend->setData(
            [
                'value' => [
                    'test' => ''
                ],
                'field_config' => [
                    'field' => 'header_logo_src'
                ],
            ]
        );
        $this->fileBackend->beforeSave();
    }

    /**
     * Test for beforeSave method with existing file.
     *
     * @throws LocalizedException
     */
    public function testBeforeSaveWithExistingFile()
    {
        $value = 'filename.jpg';
        $this->fileBackend->setData(
            [
                'value' => [
                    [
                        'url' => 'http://magento2.com/pub/media/tmp/image/' . $value,
                        'file' => $value,
                        'size' => 234234,
                        'exists' => true
                    ]
                ],
            ]
        );

        $this->fileBackend->beforeSave();
        $this->assertEquals(
            $value,
            $this->fileBackend->getValue()
        );
    }

    /**
     * Test for getRelativeMediaPath method.
     *
     * @param string $path
     * @param string $filename
     * @dataProvider getRelativeMediaPathDataProvider
     * @throws \ReflectionException
     */
    public function testGetRelativeMediaPath(string $path, string $filename)
    {
        $reflection = new \ReflectionClass($this->fileBackend);
        $method = $reflection->getMethod('getRelativeMediaPath');
        $method->setAccessible(true);
        $this->assertEquals(
            $filename,
            $method->invoke($this->fileBackend, $path . $filename)
        );
    }

    /**
     * Data provider for testGetRelativeMediaPath.
     *
     * @return array
     */
    public function getRelativeMediaPathDataProvider(): array
    {
        return [
            'Normal path' => ['pub/media/', 'filename.jpg'],
            'Complex path' => ['some_path/pub/media/', 'filename.jpg'],
        ];
    }
}
