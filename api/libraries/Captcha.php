<?php
/**
 * 生成认证图片
 *
 * @category   Custom
 * @package    Custom_Model
 * @copyright  Copyright (c) 2008 olomo. (http://www.olomo.com)
 * @Contributed by Crane
 */

class CI_Captcha
{
    /**
     * 图片宽度
     */
    private $_width = 87;

    /**
     * 图片高度
     */
    private $_height = 23;

    private $_space = 'CaptchaCode';

    /**
     * 字体路径
     */
    private $_fontPath;

    /**
     * 图片
     *
     * @var    mixed
     */
    public $image = null;

    /**
     * 对象初始化
     *
     * @param    int    $width
     * @param    int    $height
     * @return   void
     */
    public function __construct($space = NULL, $width = 75, $height = 25)
    {
        if ($space) $this->_space = $space;
        if ($width > 0) $this->_width = $width;
        if ($height > 0) $this->_height = $height;
        $this->_fontPath = SYSDIR.'/fonts/svenings.ttf';
    }

    /**
     * 验证字符串是否正确
     *
     * @param    string    $word
     * @return   bool
     */
    public function checkCode($word)
    {
        if (!isset($_SESSION[$this->_space]['code'])) {
            return false;
        }

        $word = strtoupper($word);
        $word = preg_replace('/[^A-Z0-9]/', '', $word);
        $word = base64_encode($this->encryptsCode($word));

        $recorded = $_SESSION[$this->_space]['code'];
        //unset($_SESSION[$this->_space]['code']);

        return (bool)($word === $recorded);
    }

    /**
     * 创建并输出图片
     *
     * @param    void
     * @return   void
     */
    public function create_image()
    {
        $word = $this->randomCode();

        // 记录字符串
        $_SESSION[$this->_space]['code'] = base64_encode($this->encryptsCode($word));
        $this->image = imageCreate($this->_width, $this->_height);
        imagecolorallocate($this->image, 220, 220, 220);

        // 在图片上添加扰乱元素
        $this->disturbPixel();
        
        // 在图片上添加字符串
        $this->drawCode($word);
        header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');
        header('Content-type: image/png');
        imagepng($this->image);
        imagedestroy($this->image);
    }

    /**
     * 创建扰乱元素
     *
     * @param    void
     * @return   void
     */
    private function disturbPixel()
    {
        for ($i = 1; $i <= 100; $i++) {
            $disturbColor = imagecolorallocate ($this->image, rand(0,255), rand(0,255), rand(0,255));
            imagesetpixel($this->image, rand(2,128), rand(2,38), $disturbColor);
        }
        for ($i = 0; $i < 5; $i++) {
           imageline($this->image,rand(0,20),rand(0,25),rand(90,100),rand(20,60),$disturbColor);
        }
    }

    /**
     * 在图片上添加字符串
     *
     * @param    string    $word
     * @return   void
     */
    private function drawCode($word)
    {
        for ($i = 0; $i<strlen($word); $i++) {
            $color = imagecolorallocate($this->image, rand(0,255), rand(0,128), rand(0,255));
            $x = floor($this->_width/strlen($word))*$i;
            $y = rand(0, $this->_height-15);
         // imageChar($this->image, rand(3,6), $x, $y, $word[$i], $color);
            imagettftext($this->image,14,0, $x, $y+15, $color, $this->_fontPath, $word[$i]);
        }
    }

    /**
     * 编码字符串
     *
     * @param    string    $word
     * @return   string
     */
    private function encryptsCode($word)
    {
        return substr(md5($word), 1, 10);
    }

    /**
     * 创建字符串
     *
     * @param    int    $length
     * @return   string
     */
    private function randomCode($length = 5)
    {
        $chars = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        for ($i = 0, $count = strlen($chars); $i < $count; $i++) {
            $arr[$i] = $chars[$i];
        }
        mt_srand((double) microtime() * 1000000);
        shuffle($arr);
        return substr(implode('', $arr), 5, $length);
    }
}