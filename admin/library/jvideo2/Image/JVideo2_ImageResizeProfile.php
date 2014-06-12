<?php

class JVideo2_ImageResizeProfile
{
	/** @var int Maximum width of image */
	public $maxWidth;
	/** @var int Maximum height of image */
	public $maxHeight;
	/** @var boolean If true, image will be cropped to fix aspect ration implied by $maxWidth and $maxHeight */
	public $constrainAspect;

	public function __construct($maxWidth = null, $maxHeight = null, $constrainAspect = false)
	{
		$this->maxWidth = $maxWidth;
		$this->maxHeight = $maxHeight;
		$this->constrainAspect = $constrainAspect;
	}
}