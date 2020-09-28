<?php
$oCaptcha        = new Captcha();
if($_GET['Event'] == 'display') $oCaptcha->display();
if($_GET['Event'] == 'verify')  $oCaptcha->verify();

class Captcha
{
    private $sRand;
    public function __construct()
	{
	    $this->sRand = $this->generateRand($len = 5);
		return true;
	}
	private function generateRand($len = 5)
    {        
    	$chars = "23456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ";
	    $s = "";
	    for ($i = 0; $i < $len; $i++) {
	        $int         = rand(0, strlen($chars)-1);
	        $rand_letter = $chars[$int];
	        $s           = $s . $rand_letter;
	    }
	    return $s;
	}	
	public function display()
	{
		  $_SESSION['vihash'] = md5(strtolower($this->sRand));
	      $width     = 125;$height    = 50;
	      $image     = imagecreate($width, $height);
	      $bgColor   = imagecolorallocate ($image, 255, 255, 255);
	      $textColor = imagecolorallocate ($image, 0, 0, 0);
	
	      // add random noise
	      for ($i = 0; $i < 20; $i++) {
	         $rx1 = rand(0, $width);$rx2 = rand(0, $width);$ry1 = rand(0, $height);$ry2 = rand(0, $height);
	         $rcVal = rand(0, 255);
	         $rc1 = imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(100, 255));
	         imageline($image, $rx1, $ry1, $rx2, $ry2, $rc1);
	      }
		  $rc2 = imagecolorallocate($image, 0,0, 0);
	      $font = '../font/trebucit.ttf';
	      imagettftext($image, 26, 7, 10, 45, $rc2, $font, $this->sRand);
	      header("Expires: Mon, 23 Jul 1993 05:00:00 GMT");
	      header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	      header("Cache-Control: no-store, no-cache, must-revalidate");
	      header("Cache-Control: post-check=0, pre-check=0", false);
	      header("Pragma: no-cache");
	      header('Content-type: image/jpeg');
	      imagejpeg($image);
	      imagedestroy($image);
	}
	
	public function verify()
	{
	    return true;
	}
}
?>
