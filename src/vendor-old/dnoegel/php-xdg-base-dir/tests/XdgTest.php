<?php

class XdgTest extends PHPUnit_Framework_TestCase
{
    /**
     * @return \XdgBaseDir\Xdg
     */
    public function getXdg()
    {
        return new \XdgBaseDir\Xdg;
    }

    public function test_get_home_dir()
    {
        putenv('HOME=/fake-dir');
        $this->assertEquals('/fake-dir', $this->getXdg()->getHomeDir());
    }

    public function test_get_fallback_home_dir()
    {
        putenv('HOME=');
        putenv('HOMEDRIVE=C:');
        putenv('HOMEPATH=fake-dir');
        $this->assertEquals('C:/fake-dir', $this->getXdg()->getHomeDir());
    }

    public function test_xdg_put_cache()
    {
        putenv('XDG_DATA_HOME=tmp/');
        putenv('XDG_CONFIG_HOME=tmp/');
        putenv('XDG_CACHE_HOME=tmp/');
        $this->assertEquals('tmp/', $this->getXdg()->getHomeCacheDir());
    }

    public function test_xdg_put_data()
    {
        putenv('XDG_DATA_HOME=tmp/');
        $this->assertEquals('tmp/', $this->getXdg()->getHomeDataDir());
    }

    public function test_xdg_put_config()
    {
        putenv('XDG_CONFIG_HOME=tmp/');
        $this->assertEquals('tmp/', $this->getXdg()->getHomeConfigDir());
    }

    public function test_xdg_data_dirs_should_include_home_data_dir()
    {
        putenv('XDG_DATA_HOME=tmp/');
        putenv('XDG_CONFIG_HOME=tmp/');

        $this->assertArrayHasKey('tmp/', array_flip($this->getXdg()->getDataDirs()));
    }

    public function test_xdg_config_dirs_should_include_home_config_dir()
    {
        putenv('XDG_CONFIG_HOME=tmp/');

        $this->assertArrayHasKey('tmp/', array_flip($this->getXdg()->getConfigDirs()));
    }

    /**
     * If XDG_RUNTIME_DIR is set, it should be returned
     */
    public function test_get_runtime_dir()
    {
        putenv('XDG_RUNTIME_DIR=/tmp/');
        $runtimeDir = $this->getXdg()->getRuntimeDir();

        $this->assertEquals(is_dir($runtimeDir), true);
    }

    /**
     * In strict mode, an exception should be shown if XDG_RUNTIME_DIR does not exist
     *
     * @expectedException \RuntimeException
     */
    public function test_get_runtime_dir_should_throw_exception()
    {
        putenv('XDG_RUNTIME_DIR=');
        $this->getXdg()->getRuntimeDir(true);
    }

    /**
     * In fallback mode a directory should be created
     */
    public function test_get_runtime_dir_should_create_directory()
    {
        putenv('XDG_RUNTIME_DIR=');
        $dir = $this->getXdg()->getRuntimeDir(false);
        $permission = decoct(fileperms($dir) & 0777);
        $this->assertEquals(700, $permission);
    }

    /**
     * Ensure, that the fallback directories are created with correct permission
     */
    public function test_get_runtime_should_delete_dirs_with_wrong_permission()
    {
        $runtimeDir = sys_get_temp_dir().DIRECTORY_SEPARATOR.XdgBaseDir\Xdg::RUNTIME_DIR_FALLBACK.getenv('USER');

        rmdir($runtimeDir);
        mkdir($runtimeDir, 0764, true);

        // Permission should be wrong now
        $permission = decoct(fileperms($runtimeDir) & 0777);
        $this->assertEquals(764, $permission);

        putenv('XDG_RUNTIME_DIR=');
        $dir = $this->getXdg()->getRuntimeDir(false);

        // Permission should be fixed
        $permission = decoct(fileperms($dir) & 0777);
        $this->assertEquals(700, $permission);
    }
}
