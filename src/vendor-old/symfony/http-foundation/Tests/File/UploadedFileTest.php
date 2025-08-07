<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Tests\File;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadedFileTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (! ini_get('file_uploads')) {
            $this->markTestSkipped('file_uploads is disabled in php.ini');
        }
    }

    public function test_construct_when_file_not_exists()
    {
        $this->setExpectedException('Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException');

        new UploadedFile(
            __DIR__.'/Fixtures/not_here',
            'original.gif',
            null
        );
    }

    public function test_file_uploads_with_no_mime_type()
    {
        $file = new UploadedFile(
            __DIR__.'/Fixtures/test.gif',
            'original.gif',
            null,
            filesize(__DIR__.'/Fixtures/test.gif'),
            UPLOAD_ERR_OK
        );

        $this->assertEquals('application/octet-stream', $file->getClientMimeType());

        if (extension_loaded('fileinfo')) {
            $this->assertEquals('image/gif', $file->getMimeType());
        }
    }

    public function test_file_uploads_with_unknown_mime_type()
    {
        $file = new UploadedFile(
            __DIR__.'/Fixtures/.unknownextension',
            'original.gif',
            null,
            filesize(__DIR__.'/Fixtures/.unknownextension'),
            UPLOAD_ERR_OK
        );

        $this->assertEquals('application/octet-stream', $file->getClientMimeType());
    }

    public function test_guess_client_extension()
    {
        $file = new UploadedFile(
            __DIR__.'/Fixtures/test.gif',
            'original.gif',
            'image/gif',
            filesize(__DIR__.'/Fixtures/test.gif'),
            null
        );

        $this->assertEquals('gif', $file->guessClientExtension());
    }

    public function test_guess_client_extension_with_incorrect_mime_type()
    {
        $file = new UploadedFile(
            __DIR__.'/Fixtures/test.gif',
            'original.gif',
            'image/jpeg',
            filesize(__DIR__.'/Fixtures/test.gif'),
            null
        );

        $this->assertEquals('jpeg', $file->guessClientExtension());
    }

    public function test_error_is_ok_by_default()
    {
        $file = new UploadedFile(
            __DIR__.'/Fixtures/test.gif',
            'original.gif',
            'image/gif',
            filesize(__DIR__.'/Fixtures/test.gif'),
            null
        );

        $this->assertEquals(UPLOAD_ERR_OK, $file->getError());
    }

    public function test_get_client_original_name()
    {
        $file = new UploadedFile(
            __DIR__.'/Fixtures/test.gif',
            'original.gif',
            'image/gif',
            filesize(__DIR__.'/Fixtures/test.gif'),
            null
        );

        $this->assertEquals('original.gif', $file->getClientOriginalName());
    }

    public function test_get_client_original_extension()
    {
        $file = new UploadedFile(
            __DIR__.'/Fixtures/test.gif',
            'original.gif',
            'image/gif',
            filesize(__DIR__.'/Fixtures/test.gif'),
            null
        );

        $this->assertEquals('gif', $file->getClientOriginalExtension());
    }

    /**
     * @expectedException \Symfony\Component\HttpFoundation\File\Exception\FileException
     */
    public function test_move_local_file_is_not_allowed()
    {
        $file = new UploadedFile(
            __DIR__.'/Fixtures/test.gif',
            'original.gif',
            'image/gif',
            filesize(__DIR__.'/Fixtures/test.gif'),
            UPLOAD_ERR_OK
        );

        $movedFile = $file->move(__DIR__.'/Fixtures/directory');
    }

    public function test_move_local_file_is_allowed_in_test_mode()
    {
        $path = __DIR__.'/Fixtures/test.copy.gif';
        $targetDir = __DIR__.'/Fixtures/directory';
        $targetPath = $targetDir.'/test.copy.gif';
        @unlink($path);
        @unlink($targetPath);
        copy(__DIR__.'/Fixtures/test.gif', $path);

        $file = new UploadedFile(
            $path,
            'original.gif',
            'image/gif',
            filesize($path),
            UPLOAD_ERR_OK,
            true
        );

        $movedFile = $file->move(__DIR__.'/Fixtures/directory');

        $this->assertFileExists($targetPath);
        $this->assertFileNotExists($path);
        $this->assertEquals(realpath($targetPath), $movedFile->getRealPath());

        @unlink($targetPath);
    }

    public function test_get_client_original_name_sanitize_filename()
    {
        $file = new UploadedFile(
            __DIR__.'/Fixtures/test.gif',
            '../../original.gif',
            'image/gif',
            filesize(__DIR__.'/Fixtures/test.gif'),
            null
        );

        $this->assertEquals('original.gif', $file->getClientOriginalName());
    }

    public function test_get_size()
    {
        $file = new UploadedFile(
            __DIR__.'/Fixtures/test.gif',
            'original.gif',
            'image/gif',
            filesize(__DIR__.'/Fixtures/test.gif'),
            null
        );

        $this->assertEquals(filesize(__DIR__.'/Fixtures/test.gif'), $file->getSize());

        $file = new UploadedFile(
            __DIR__.'/Fixtures/test',
            'original.gif',
            'image/gif'
        );

        $this->assertEquals(filesize(__DIR__.'/Fixtures/test'), $file->getSize());
    }

    public function test_get_extension()
    {
        $file = new UploadedFile(
            __DIR__.'/Fixtures/test.gif',
            'original.gif',
            null
        );

        $this->assertEquals('gif', $file->getExtension());
    }

    public function test_is_valid()
    {
        $file = new UploadedFile(
            __DIR__.'/Fixtures/test.gif',
            'original.gif',
            null,
            filesize(__DIR__.'/Fixtures/test.gif'),
            UPLOAD_ERR_OK,
            true
        );

        $this->assertTrue($file->isValid());
    }

    /**
     * @dataProvider uploadedFileErrorProvider
     */
    public function test_is_invalid_on_upload_error($error)
    {
        $file = new UploadedFile(
            __DIR__.'/Fixtures/test.gif',
            'original.gif',
            null,
            filesize(__DIR__.'/Fixtures/test.gif'),
            $error
        );

        $this->assertFalse($file->isValid());
    }

    public function uploadedFileErrorProvider()
    {
        return [
            [UPLOAD_ERR_INI_SIZE],
            [UPLOAD_ERR_FORM_SIZE],
            [UPLOAD_ERR_PARTIAL],
            [UPLOAD_ERR_NO_TMP_DIR],
            [UPLOAD_ERR_EXTENSION],
        ];
    }

    public function test_is_invalid_if_not_http_upload()
    {
        $file = new UploadedFile(
            __DIR__.'/Fixtures/test.gif',
            'original.gif',
            null,
            filesize(__DIR__.'/Fixtures/test.gif'),
            UPLOAD_ERR_OK
        );

        $this->assertFalse($file->isValid());
    }
}
