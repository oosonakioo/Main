<?php

namespace Faker\Test\Provider;

use Faker\Provider\Image;

class ImageTest extends \PHPUnit_Framework_TestCase
{
    public function test_image_url_uses640x680_as_the_default_size()
    {
        $this->assertRegExp('#^http://lorempixel.com/640/480/#', Image::imageUrl());
    }

    public function test_image_url_accepts_custom_width_and_height()
    {
        $this->assertRegExp('#^http://lorempixel.com/800/400/#', Image::imageUrl(800, 400));
    }

    public function test_image_url_accepts_custom_category()
    {
        $this->assertRegExp('#^http://lorempixel.com/800/400/nature/#', Image::imageUrl(800, 400, 'nature'));
    }

    public function test_image_url_accepts_custom_text()
    {
        $this->assertRegExp('#^http://lorempixel.com/800/400/nature/Faker#', Image::imageUrl(800, 400, 'nature', false, 'Faker'));
    }

    public function test_image_url_adds_a_random_get_parameter_by_default()
    {
        $url = Image::imageUrl(800, 400);
        $splitUrl = preg_split('/\?/', $url);

        $this->assertEquals(count($splitUrl), 2);
        $this->assertRegexp('#\d{5}#', $splitUrl[1]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test_url_with_dimensions_and_bad_category()
    {
        Image::imageUrl(800, 400, 'bullhonky');
    }

    public function test_download_with_defaults()
    {
        $url = 'http://www.lorempixel.com/';
        $curlPing = curl_init($url);
        curl_setopt($curlPing, CURLOPT_TIMEOUT, 5);
        curl_setopt($curlPing, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($curlPing, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($curlPing);
        $httpCode = curl_getinfo($curlPing, CURLINFO_HTTP_CODE);
        curl_close($curlPing);

        if ($httpCode < 200 | $httpCode > 300) {
            $this->markTestSkipped('LoremPixel is offline, skipping image download');
        }

        $file = Image::image(sys_get_temp_dir());
        $this->assertFileExists($file);
        if (function_exists('getimagesize')) {
            [$width, $height, $type, $attr] = getimagesize($file);
            $this->assertEquals(640, $width);
            $this->assertEquals(480, $height);
            $this->assertEquals(constant('IMAGETYPE_JPEG'), $type);
        } else {
            $this->assertEquals('jpg', pathinfo($file, PATHINFO_EXTENSION));
        }
        if (file_exists($file)) {
            unlink($file);
        }
    }
}
