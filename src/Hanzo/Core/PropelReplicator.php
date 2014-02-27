<?php

namespace Hanzo\Core;


class PropelReplicator
{
    protected $connections;

    /**
     * @param \PropelConfiguration $configuration
     */
    public function __construct(\PropelConfiguration $configuration)
    {
        $this->parseConfiguration($configuration);
    }


    /**
     * @param string $sql
     * @param array  $parameters
     * @return array
     */
    public function executeQuery($sql, array $parameters = [])
    {
        $results = [];
        foreach ($this->connections as $name) {
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
