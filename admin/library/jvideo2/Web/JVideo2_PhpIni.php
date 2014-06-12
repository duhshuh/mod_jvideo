<?php
class JVideo2_PhpIni
{

    public function getBool($optionName)
    {
        $value = ini_get($optionName);

        switch (strtolower($value))
        {
			case 'on':
			case 'yes':
			case 'true':
				return 'assert.active' !== $optionName;

			case 'stdout':
			case 'stderr':
				return 'display_errors' === $optionName;

			default:
				return (bool) (int) $value;
        }
    }
}