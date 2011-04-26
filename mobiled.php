<?php

/**
 * mobiled
 *
 * @description mobile browser detection
 * @copyright Copyright (c) 2009 Filip Oscadal <filip@mxd.cz>
 * @license GNU General Public License
 * @link http://mobiled.mxd.cz
 * @package mobiled
 * @category mobile
 * @version 1.0.1 (2009-07-17)
 */

/**
 * @package mobiled
 * @author Filip Ošèádal
 * @copyright Copyright (c) 2009 Filip Oscadal <filip@mxd.cz>
 */
class mobiled
{

  /** @var bool is it mobile browser? */
  private $mobile = false;
  /** @var array matched agent strings */
  private $version = array();

  /**
   * Detect mobile browser and set appropriate headers
   * @return mixed returns false for non-mobile browser or it's identification string
   */
  public function __construct()
  {
    if (version_compare(PHP_VERSION, '5.2.0', '<'))
    {
      throw new Exception('Requires PHP 5.2.0 or newer.');
    }
    $this->detect();
    $this->setHeader();
    return ($this->mobile) ? $this->getVersion() : false;
  }

  /**
   * Detection routine
   * @return bool true = mobile, false = other browsers
   */
  public function detect()
  {
    $this->mobile = false;
    if (preg_match('/(android|avantgo|blackberry|blazer|elaine|hiptop|iphone|ipod|kindle|midp|mmp|mobile|o2|opera mini|palm|palm os|pda|plucker|pocket|psp|smartphone|symbian|treo|up.browser|up.link|vodafone|wap|windows ce; iemobile|windows ce; ppc;|windows ce; smartphone;|xiino)/i', $_SERVER['HTTP_USER_AGENT'], $this->version)) $this->mobile = true;
    if (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE'])) $this->mobile = true;
    if ((strpos($_SERVER['HTTP_ACCEPT'], 'text/vnd.wap.wml') > 0) || (strpos($_SERVER['HTTP_ACCEPT'], 'application/vnd.wap.xhtml+xml') > 0)) $this->mobile = true;
    return $this->mobile;
  }

  /**
   * Send mobile HTTP headers
   * @return bool true = headers sent, 0 = headers not sent
   */
  public function setHeader()
  {
    if (($this->mobile) && (!headers_sent()))
    {
      // this directive indicates that (most of) the response must not be transformed
      header('Cache-Control: no-transform');
      header('Vary: User-Agent, Accept');
      return true;
    }
    return false;
  }

  /**
   * Get mobile version string
   * @return string detected mobile browser string
   */
  public function getVersion()
  {
    $this->detect();
    return (isset($this->version[1])) ? $this->version[1] : '';
  }

}

?>