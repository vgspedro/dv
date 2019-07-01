<?php
namespace App\Service;

class ForegroundBackgroundColorGenerator
{
	// for good read the value must be greater than 4 
	// max value is 15 else exceeds memory alocation
	private $minContrast = 5;
	private $maxContrast = 15;
	
	/**

	*/
	public function getColorRgb($t = null, $c = null){

		$color = $this->interactions($this->setTextColor($t), $this->setContrast($c));

		return $color;
	}

	/**

	*/
	public function getColorHex($t = null, $c = null){

		$color = $this->interactions($this->setTextColor($t), $this->setContrast($c));

		$text = explode(',', $color['textcolor']);
		$background = explode(',', $color['background']);

		$hexText = '#';
		$hexBack = '#';

		for($i = 0; $i < count($text); $i++){
			$hexText .= str_pad(dechex($text[$i]), 2, "0", STR_PAD_LEFT);
			$hexBack .= str_pad(dechex($background[$i]), 2, "0", STR_PAD_LEFT);
		}
		
		$color['textcolor'] = $hexText;
		$color['background'] = $hexBack;

		return $color;
	}

	/**

	*/
	private function lumDiff($text = null, $c = null)
	{

		if($text){
			$toDecimal = str_split($text, 2);
			$r2 = hexdec($toDecimal[0]);
			$g2 = hexdec($toDecimal[1]);
			$b2 = hexdec($toDecimal[2]);	
		}

		else{
			$r2 = $this->randomColorPart();
			$g2 = $this->randomColorPart();
			$b2 = $this->randomColorPart();
		}

		$r1 = $this->randomColorPart();
		$g1 = $this->randomColorPart();
		$b1 = $this->randomColorPart();

    	$l1 = 0.2126 * pow($r1/255, 2.2) +
          0.7152 * pow($g1/255, 2.2) +
          0.0722 * pow($b1/255, 2.2);
    	
    	$l2 = 0.2126 * pow($r2/255, 2.2) +
          0.7152 * pow($g2/255, 2.2) +
          0.0722 * pow($b2/255, 2.2);
 
    	//for good read the value $c must be greater than 5 
    	$isReadable = $l1 > $l2 ? ($l1+0.05) / ($l2+0.05) : ($l2+0.05) / ($l1+0.05);

		$color = $isReadable >= $c ? $r1.','.$g1.','.$b1 : null;

		$a = array('textcolor' =>  $r2.','.$g2.','.$b2 , 'background' => $color, 'contrast' => $c);

		return $a;
    }

/**

*/
    private function interactions($t,$c){

    	$interactions = 0; 

		$a = $this->lumDiff($t, $c);

		$a['interactions'] = 0;

		if(!$a['background']){ 
			while(!$a['background']){ 

				$a = $this->lumDiff($t, $c);
				$interactions++;
				$a['interactions'] = $interactions;
			}
		}
		return $a;
    }

	/**
	*/
	private function setTextColor($text = null){

		$neutralColors = array("000000", "111111", "222222","cccccc","dddddd","eeeeee","ffffff");
		
		if($text && strlen($text) == 6){
			$text = strtolower($text);
			$text = in_array($text, $neutralColors) ? $text : null;
		}

		return $text;
	}

	/**

	*/
	private function setContrast($contrast = null){
	
		if(!$contrast)
			$contrast = $this->minContrast;
		
		elseif($contrast > 15)
			$contrast = $this->maxContrast;
	
		return $contrast;
	}

	/**
	*/
	private function randomColorPart() {
		$decimal = str_pad( mt_rand( 0, 255 ), 2, '0', STR_PAD_LEFT);
    	return $decimal;
	}
	
}
