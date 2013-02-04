<?php

namespace Kodify\TranscoderBundle\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Parser;
use Kodify\TranscoderBundle\Tests\TestBaseClass;

use Kodify\TranscoderBundle\DependencyInjection\KodifyTranscoderExtension;

class KodifyTranscoderExtensionTest extends TestBaseClass
{

    protected $object;

    public function setUp()
    {
        parent::setUp();
        $this->object = new KodifyTranscoderExtension();
    }

    public function getNonValidConfigValues()
    {
        return array(
            array('arrayWithoutContent' => array()),
            array('completelyNull' => null),
            array('onlyDriver' => array('driver' => 'supu')),
            array('driverIncorrectAccessKey' => array('driver' => 'supu', 'tupu_access_key' => 'tupu')),
            array('withAccessKey' => array('driver' => 'supu', 'supu_access_key' => 'tupu')),
            array('withSecretKey' => array('driver' => 'supu', 'supu_access_key' => 'tupu', 'supu_secret_key' => 'tupu')),
            array('withApiUrl' => array(
                'driver' => 'supu', 'supu_access_key' => 'tupu', 'supu_secret_key' => 'tupu', 'supu_api_url' => 'tupu')
            ),
            array('withCloudId' => array(
                'driver' => 'supu', 'supu_access_key' => 'tupu', 'supu_secret_key' => 'tupu', 'supu_api_url' => 'tupu',
                'supu_cloud_id' => 'tupu'
            )
            )
        );
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @dataProvider getNonValidConfigValues
     */
    public function testNonValidConfigValues($config)
    {
        $res = $this->object->load(array($config), $container = new ContainerBuilder());
    }

    /**
     * @dataProvider parameterValues
     */
    public function testLoadconfig($name, $expected)
    {
        $config = $this->parseYaml($this->getYamlConfig());

        $this->object->load(array($config), $container = new ContainerBuilder());

        $this->assertEquals($expected, $container->getParameter($name));

    }

    private function parseYaml($yaml)
    {
        $parser = new Parser();

        return $parser->parse($yaml);
    }

    private function getYamlConfig()
    {
        return <<<'EOF'
driver: pandastream
pandastream_access_key: pandastream_access_key
pandastream_secret_key: pandastream_secret_key
pandastream_api_url: pandastream_api_url
pandastream_cloud_id: pandastream_cloud_id
EOF;
    }

    public static function parameterValues()
    {
        return array(
            array('kodify_transcoder.service.driver', 'pandastream'),
            array('kodify_transcoder.service.accessKey', 'pandastream_access_key'),
            array('kodify_transcoder.service.secretKey', 'pandastream_secret_key'),
            array('kodify_transcoder.service.apiUrl', 'pandastream_api_url'),
            array('kodify_transcoder.service.cloudId', 'pandastream_cloud_id'),
        );
    }
}