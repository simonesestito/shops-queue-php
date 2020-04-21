<?php


class Dao {
    /** @var $db mysqli */
    protected $db;

    public function __construct(mysqli $db) {
        $this->db = $db;
    }

    /**
     * Sanitise a given string
     * @param string $value String to sanitise
     * @return string Sanitised string
     */
    protected function sanitize(string $value): string {
        return $this->db->real_escape_string($value);
    }

    /**
     * Execute a query.
     * It bind the given parameters,
     * and handles known MySQL errno
     * @param string $query SQL query
     * @param mixed[] $params Params to bind to the query
     * @return array|int Array of found records, or ID of latest INSERT
     * @throws DuplicateEntityException
     */
    protected function query(string $query, array $params = []) {
        $statement = $this->db->prepare($query);
        if (!empty($params)) {
            $types = '';
            foreach ($params as $param) {
                if (is_string($param))
                    $types .= 's';
                elseif (is_int($param))
                    $types .= 'i';
                elseif (is_bool($param))
                    $types .= 'b';
                elseif (is_double($param))
                    $types .= 'd';
                else
                    throw new RuntimeException("Unknown type for '$param'");
            }

            $statement->bind_param($types, ...$params);
        }

        $success = $statement->execute();
        if ($success) {
            // Return the result
            $result = $statement->get_result();
            if ($result)
                return $result->fetch_all(MYSQLI_ASSOC);
            else
                return $statement->insert_id;
        }

        $errno = $statement->errno;
        if ($errno === MYSQL_DUPLICATE_ERROR) {
            throw new DuplicateEntityException();
        } else {
            throw new RuntimeException($statement->error);
        }
    }
}