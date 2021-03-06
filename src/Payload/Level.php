<?php namespace Rollbar\Payload;

/**
 * @method static Level critical()
 * @method static Level error()
 * @method static Level warning()
 * @method static Level debug()
 * @method static Level info()
 * @method static Level ignored()
 * @method static Level ignore()
 */
class Level implements \JsonSerializable
{
    private static $values;

    private static function init()
    {
        if (is_null(self::$values)) {
            self::$values = array(
                "emergency" => new Level("critical", 100000),
                "alert" => new Level("critical", 100000),
                "critical" => new Level("critical", 100000),
                "error" => new Level("error", 10000),
                "warning" => new Level("warning", 1000),
                "notice" => new Level("info", 100),
                "info" => new Level("info", 100),
                "debug" => new Level("debug", 10),
                "ignored" => new Level("ignore", 0),
                "ignore" => new Level("ignore", 0)

            );
        }
    }

    public static function __callStatic($name, $args)
    {
        return self::fromName($name);
    }

    /**
     * @param string $name level name
     * @return Level
     */
    public static function fromName($name)
    {
        self::init();
        $name = strtolower($name);
        return array_key_exists($name, self::$values) ? self::$values[$name] : null;
    }

    /**
     * @var string
     */
    private $level;
    private $val;

    private function __construct($level, $val)
    {
        $this->level = $level;
        $this->val = $val;
    }

    public function __toString()
    {
        return $this->level;
    }

    public function toInt()
    {
        return $this->val;
    }

    public function jsonSerialize()
    {
        return $this->level;
    }
}
