<?php

/**
 * Inspired from sfGravatarPlugin of Mickael Kurmann and Xavier Lacot <xavier@lacot.org> for Symfony
 *
 * @package  jGravatar 
 * @author Florian Lonqueu-Brochard
 * @license  MIT
 **/
 
class jGravatar{

  // default cached gravatar img
  protected static $default_image;

  // possible to put : 3 days, 1 week, and whatever you want according to php strtotime function
  protected static $expire_ago;

  
  protected static $cache_dir;
  protected static $cache_dir_name;

  protected static $base_url = "http://www.gravatar.com";
  
  // gravatar ratings are only : G | PG | R | X
  protected static $base_ratings = array('G', 'PG', 'R', 'X');

  protected static $default_image_size = 80;
  protected static $default_rating = 'G';
  protected static $default_className = 'gravatar';
  
  private static $initialized = false;
  
  protected $image_size, $rating;
  
  
  /**
  * Constructor
  * @author Florian Lonqueu-Brochard
  *
  **/
  public function __construct($image_size = null, $rating = null)
  {

    if( self::$initialized == false ){
    
      $config = jIniFile::read(JELIX_APP_CONFIG_PATH.'defaultconfig.ini.php');
  
      self::$cache_dir = JELIX_APP_WWW_PATH.$config['jGravatar']['cache_dir'];
      self::$cache_dir_name = $GLOBALS['gJConfig']->urlengine['basePath'].$config['jGravatar']['cache_dir'];
      self::$default_image = $config['jGravatar']['default_image'];
      self::$expire_ago = $config['jGravatar']['expire_ago'];
      
      self::$initialized = true;
    }

    //image_size
    if (is_null($image_size) || $image_size > 80 || $image_size < 1)
      $this->image_size = self::$default_image_size;
    else
      $this->image_size = $image_size;

    //rating
    if (is_null($rating) || !in_array($rating, $this->base_ratings))
      $this->rating = self::$default_rating;
    else
      $this->rating = $rating;
  }


  /**
   * constructs path to gravatar (with size, rating, md5 email and a default image to redirect to (if not found))
   *
   * @return String
   * @author Mickael Kurmann
   **/
  protected function buildGravatarPath($md5_email)
  {
    return self::$base_url.'/avatar.php?gravatar_id='.$md5_email.
                           '&size='.$this->image_size.
                           '&rating='.$this->rating.
                           '&default=http://www.default.com';
  }

  /**
   * Check if a gravatar is avaible on gravatar.com
   *
   * @return boolean
   * @author Mickael Kurmann
   **/
  protected function hasGravatar($md5_email)
  {
    // TODO try cache !
    $ch = curl_init($this->buildGravatarPath($md5_email));
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    //--- Start buffering : HIDE CURL EXEC RETURN ...
    ob_start();
    curl_exec($ch);
    ob_end_clean();
    //--- End buffering and clean output

    $session_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // 200 == page with no error, else 301 == redirect (no gravatar) or 404... or whatever
    if ($session_code == 200)
    {
      return true;
    }

    return false;
  }

  /**
   * check for a cache hit - if found check if file is within expiry time
   *
   * @return void
   * @author Mickael Kurmann
   **/
  protected function isCacheValid($file_path)
  {
    if (file_exists($file_path))
    {
      if (filectime($file_path) < strtotime("+".self::$expire_ago))
      {
        // file exists and cache is valid
        return true;
      }
      else
      {
        // file exists but cache has expired
        unlink($file_path);
      }
    }

    // no file
    return false;
  }

  // get the gravatar to the cache, if email has a gravatar and it does not
  // already exist (or has expired)
  public function getGravatar($email)
  {
    $md5_email = md5( strtolower( trim( $email ) ) );
    $file = self::$cache_dir.$md5_email.'.png';

    // the cache is valid, return the cached image
    $to_return = $md5_email;

    // check the cache
    if (!$this->isCacheValid($file))
    {
      // no image in cache
      if ($this->hasGravatar($md5_email))
      {
        $path = $this->buildGravatarPath($md5_email);
      }
      else
      {
        // no gravatar --> get the default one
        $path = realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.self::$default_image);
      }

      $gravatar_img = jFile::read($path);
      jFile::write($file, $gravatar_img);
      
    }

    return str_replace(DIRECTORY_SEPARATOR, '/', self::$cache_dir_name).$to_return.'.png';
  }
  
  /**
  * Return a gravatar image for a given email
  *
  * @param string  $email            Email of the gravatar
  * @param string  $gravatar_rating  Maximal rating of the gravatar
  * @param integer $gravatar_size    size of the gravatar
  * @param string  $alt_text         Alternative text
  * @return string
  *
  * @author Florian Lonqueu-Brochard
  * @static
  */
  public static function get($email, $gravatar_size = null, $gravatar_rating = null, $alt_text = ''){
    $gravatar = new jGravatar($gravatar_rating, $gravatar_size);
    // return the gravatar image

    $class = self::$default_className ? 'class ="'.self::$default_className.'"' : '';
    $alt = $alt_text ? 'alt="'.$alt_text.'"' : 'alt=""';
    $width = 'width="'.$gravatar->image_size.'"';
    $height = 'height="'.$gravatar->image_size.'"';
    
    return '<img src="'.$gravatar->getGravatar($email).'" '.$width.' '.$height.' '.$alt.' '.$class.' />';
  }
  
  /**
  * Display a gravatar image for a given email
  *
  * @see get
  * @author Florian Lonqueu-Brochard
  * @static
  */
  public static function display($email, $gravatar_size = null, $gravatar_rating = null, $alt_text = ''){
    print self::get($email, $gravatar_size, $gravatar_rating, $alt_text);
  }
  
}

