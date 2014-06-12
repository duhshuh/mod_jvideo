<?php
require_once dirname(__FILE__) . '/../../jvideo/JVideo_Exception.php';

class JVideo2_DbBatch {
	public static function execute(JDatabaseDriver $db, $batchSql)
	{
		$queries = $db->splitSql($batchSql);

		$db->transactionStart(true);

		foreach ($queries as $query)
		{
			self::tryToExecuteQuery($db, $query);
		}

		$db->transactionCommit(true);
	}

	private static function tryToExecuteQuery($db, $query)
	{
		if (strlen(trim($query)) <= 0) return;
		
		try
		{
			$db->setQuery($query);
			if (!$db->execute()) {
				$db->transactionRollback(true);
				throw new DbBatchException($query);
			}
		}
		catch (RuntimeException $e)
		{
			$db->transactionRollback(true);
			throw new DbBatchException($query, $e);
		}
	}
}

class DbBatchException extends JVideo_Exception
{
	public function __construct($query, Exception $previous = null)
	{
		$this->query = $query;
		parent::__construct('DbBatch failed to execute query', 0, $previous);
	}
}