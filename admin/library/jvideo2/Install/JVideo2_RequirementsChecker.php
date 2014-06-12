<?php
require_once dirname(__FILE__) . '/../Database/JVideo2_MySqlInfo.php';
require_once dirname(__FILE__) . '/../Web/JVideo2_PhpInfo.php';

class JVideo2_RequirementsChecker
{
	/** @var JVideo2_PhpInfo */
	private $phpInfo;
	/** @var JVideo2_MySqlInfo */
	private $mysqlInfo;
	
	const MINIMUM_PHP_VERSION = '5.1.2';
	const MINIMUM_MYSQL_VERSION = '4.1.22';
	
	public function __construct(JVideo2_PhpInfo $phpInfo, JVideo2_MySqlInfo $mysqlInfo)
	{
		$this->phpInfo = $phpInfo;
		$this->mysqlInfo = $mysqlInfo;
	}
	
	public function getRequirementsStatus()
	{
		$status = array();
		
		$this->addPhpVersionStatus($status);
		$this->addMySqlVersionStatus($status);
		$this->addCurlStatus($status);
		$this->addLibXmlStatus($status);
		$this->addGdStatus($status);
		$this->addSafeModeStatus($status);
		
		return $status;
	}
	
	private function addPhpVersionStatus(&$status)
	{
        if ($this->isValidPhpVersion())
		{
            $status['PHP version'] = 'OK';
        }
		else
		{
            $status['PHP version'] = 'PHP ' . self::MINIMUM_PHP_VERSION . ' or higher is required';
        }
	}
	
	private function addMySqlVersionStatus(&$status)
	{
        if ($this->isValidMySqlVersion())
		{
            $status['MySQL version'] = 'OK';
        }
		else
		{
            $status['MySQL version'] = 'MySQL ' . self::MINIMUM_MYSQL_VERSION . ' or higher is required (MySQL 5 recommended)';
        }
	}
	
	private function addCurlStatus(&$status)
	{
        if ($this->phpInfo->isCurlInstalled())
		{
            $status['cURL support'] = 'OK';
        }
		else
		{
            $status['cURL support'] = "PHP module 'cURL' is required";
        }
	}
	
	private function addLibXmlStatus(&$status)
	{
        if ($this->phpInfo->isLibXmlInstalled())
		{
            if ($this->isLibXMLCompatible())
			{
                $status['libXml support'] = 'OK';
            }
			else
			{
				$version = $this->phpInfo->getLibXmlVersion();
                $status['libXml support'] = 'libXML version ' . $version . ' is NOT supported. Please see our ' .
                                            '<a href="http://jvideo.warphd.com/support/faq" target="_blank">FAQ</a>';
            }
        }
		else
		{
            $status['libXml support'] = "PHP module 'libXML' is invalid or does not exist";
        }
	}

	private function addGdStatus(&$status)
	{
		if (!$this->phpInfo->isGdInstalled())
		{
			$status['GD support'] = "PHP module 'GD' is required";
		}
		elseif (!$this->phpInfo->isGdPngSupportInstalled())
		{
			$status['GD support'] = "PHP module 'GD' is installed, but does not have PNG support";
		}
		else
		{
			$status['GD support'] = 'OK';
		}
	}
	
	private function addSafeModeStatus(&$status)
	{
        if ($this->phpInfo->isSafeModeEnabled())
		{
            $status['Safe Mode disabled'] = 'Safe mode must be disabled';
        }
		else
		{
            $status['Safe Mode disabled'] = 'OK';
        }
	}
	
    private function isValidPhpVersion()
    {
        return version_compare($this->phpInfo->getVersion(), self::MINIMUM_PHP_VERSION, '<') === false;
    }

    private function isValidMySqlVersion()
    {
        return version_compare($this->mysqlInfo->getVersion(), self::MINIMUM_MYSQL_VERSION, '<') === false;
    }

    private function isLibXMLCompatible()
    {
        return $this->phpInfo->getLibXmlVersion() != '2.7.1'
            && $this->phpInfo->getLibXmlVersion() != '2.7.2';
    }
}
