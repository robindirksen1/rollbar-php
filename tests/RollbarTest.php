<?php namespace Rollbar;

if (!defined('ROLLBAR_TEST_TOKEN')) {
    define('ROLLBAR_TEST_TOKEN', 'ad865e76e7fb496fab096ac07b1dbabb');
}

use Rollbar\Rollbar;
use Rollbar\Payload\Level;

/**
 * Usage of static method Rollbar::logger() is intended here.
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class RollbarTest extends \PHPUnit_Framework_TestCase
{

    private static $simpleConfig = array(
        'access_token' => ROLLBAR_TEST_TOKEN,
        'environment' => 'test'
    );

    private static function clearLogger()
    {
        $reflLoggerProperty = new \ReflectionProperty('Rollbar\Rollbar', 'logger');
        $reflLoggerProperty->setAccessible(true);
        $reflLoggerProperty->setValue(null);
    }
    
    public static function setupBeforeClass()
    {
        self::clearLogger();
    }

    public function tearDown()
    {
        self::clearLogger();
    }
    
    public function testInitWithConfig()
    {
        Rollbar::init(self::$simpleConfig);
        
        $this->assertInstanceOf('Rollbar\RollbarLogger', Rollbar::logger());
        $this->assertAttributeEquals(new Config(self::$simpleConfig), 'config', Rollbar::logger());
    }
    
    public function testInitWithLogger()
    {
        $logger = $this->getMockBuilder('Rollbar\RollbarLogger')->disableOriginalConstructor()->getMock();

        Rollbar::init($logger);
        
        $this->assertSame($logger, Rollbar::logger());
    }
    
    public function testInitConfigureLogger()
    {
        $logger = $this->getMockBuilder('Rollbar\RollbarLogger')->disableOriginalConstructor()->getMock();
        $logger->expects($this->once())->method('configure')->with(self::$simpleConfig);

        Rollbar::init($logger);
        Rollbar::init(self::$simpleConfig);
    }
    
    public function testInitReplaceLogger()
    {
        Rollbar::init(self::$simpleConfig);

        $this->assertInstanceOf('Rollbar\RollbarLogger', Rollbar::logger());

        $logger = $this->getMockBuilder('Rollbar\RollbarLogger')->disableOriginalConstructor()->getMock();

        Rollbar::init($logger);

        $this->assertSame($logger, Rollbar::logger());
    }

    public function testLogException()
    {
        Rollbar::init(self::$simpleConfig);

        try {
            throw new \Exception('test exception');
        } catch (\Exception $e) {
            Rollbar::log(Level::error(), $e);
        }
        
        $this->assertTrue(true);
    }
    
    public function testLogMessage()
    {
        Rollbar::init(self::$simpleConfig);

        Rollbar::log(Level::info(), 'testing info level');
        $this->assertTrue(true);
    }
    
    public function testLogExtraData()
    {
        Rollbar::init(self::$simpleConfig);

        Rollbar::log(
            Level::info(),
            'testing extra data',
            array("some_key" => "some value") // key-value additional data
        );
        
        $this->assertTrue(true);
    }

    /**
     * Below are backwards compatibility tests with v0.18.2
     */
    public function testBackwardsSimpleMessageVer()
    {
        Rollbar::init(self::$simpleConfig);

        $uuid = Rollbar::report_message("Hello world");
        $this->assertStringMatchesFormat('%x-%x-%x-%x-%x', $uuid);
    }
    
    public function testBackwardsSimpleError()
    {
        Rollbar::init(self::$simpleConfig);
        
        $result = Rollbar::report_php_error(E_ERROR, "Runtime error", "the_file.php", 1);
        // always returns false.
        $this->assertFalse($result);
    }
    
    public function testBackwardsSimpleException()
    {
        Rollbar::init(self::$simpleConfig);
        
        $uuid = null;
        try {
            throw new \Exception("test exception");
        } catch (\Exception $e) {
            $uuid = Rollbar::report_exception($e);
        }

        $this->assertStringMatchesFormat('%x-%x-%x-%x-%x', $uuid);
    }

    public function testBackwardsFlush()
    {
        Rollbar::init(self::$simpleConfig);

        Rollbar::flush();
        $this->assertTrue(true);
    }
}
