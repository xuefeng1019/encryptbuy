<?php
//here we go!
F::init();

class F
{
    private static  $instances = array();
    public static $f = NULL;
    
    private function __construct(){}
    
    
    public static function init()
    {
        if(self::$f === NULL)
        {
            self::$f = new self();
        }
    }
    public static function getInstance() 
    {
        $args = func_get_args();
        $class = array_shift($args);

        if(!class_exists($class)) 
        {
            autoload($class); 
        }
        
        if(class_exists($class)) 
        {
            $class = ucwords($class);
            $key = strtolower($class) . implode('|', $args);
            if(isset(F::$instances[$key]))
            {
                return F::$instances[$key];
            }
            
            $instance = NULL;
            switch(count($args))
            {
                case 1: $instance = new $class($args[0]); break;
                case 2: $instance = new $class($args[0], $args[1]);break;
                case 3: $instance = new $class($args[0], $args[1], $args[2]);break;
                case 4: $instance = new $class($args[0], $args[1], $args[2], $args[3]);break;
                default: $instance = new $class();break;
            }
            
            F::$instances[$key] = $instance;
            return $instance;
        }
        else
        {
            show_error("$class isn`t founded when F class try to init it!");
        }
    }
    
    public function __call($name, $args)
    {
        if(empty($args))
        {
            $args = array();
        }
        
        array_unshift($args, $name);
        return call_user_func_array(array('F', 'getInstance'), $args);
    }
    
    public function __get($name)
    {
        return self::__call($name, NULL);
    }
}
