<?php

namespace Hanzo\Core;


class PropelReplicator
{
    /**
     * @var array
     */
    protected $connections = [];

    /**
     * @param \PropelConfiguration $configuration
     */
    public function __construct(\PropelConfiguration $configuration)
    {
        $this->parseConfiguration($configuration);
    }


    /**
     * Executes a sql statement across all linked databases.
     * The function will return an array of PDOStatement results
     *
     * @param  string $sql                   The sql query to execute.
     * @param  array  $parameters            Optional array of PDOStatement::bindValue parameters
     * @param  array  $use_named_connections Optional array of connection names to use, if set only these named connections will be used.
     * @return array                         An array of PDOStatement results
     */
    public function executeQuery($sql, array $parameters = [], array $use_named_connections = [])
    {
        $results = [];

        $connections = $this->connections;
        if (count($use_named_connections)) {
            $connections = $use_named_connections;
        }

        foreach ($connections as $name) {
            $connection = \Propel::getConnection($name, \Propel::CONNECTION_WRITE);
            $statement = $connection->prepare($sql);

            foreach ($parameters as $k => $v) {
                $statement->bindValue($k, $v);
            }

            $statement->execute();
            $results[$name] = $statement;
        }

        return $results;
    }


    /**
     * Returns an array with the discovered connection names.
     *
     * @return array
     */
    public function getConnectionNames()
    {
        return $this->connections;
    }


    /**
     * Parses the Propel configuration to get a list of actual connections
     *
     * @param \PropelConfiguration $configuration
     */
    protected function parseConfiguration(\PropelConfiguration $configuration)
    {
        foreach ($configuration->getFlattenedParameters() as $key => $value) {
            list($namespace, $name, $rest) = explode('.', $key, 3);

            // only add one connection, and only if the user is set
            if (($rest == 'connection.user') &&
                ($namespace == 'datasources')
            ) {
                $value = trim($value);
                if (!empty($value)) {
                    $this->connections[$name] = $name;
                }
            }
        }

    }
}
