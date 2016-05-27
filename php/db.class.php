<?php

namespace TSAState;

require_once $_SERVER['DOCUMENT_ROOT'].'/../../'.$_SERVER["SERVER_NAME"].'.config.php';

global $CONFIG;

final class Db
{
    /**
     * Currrent database connection. Automagically opened and closed as needed.
     * @var MySQLi database connection object
     */
    private $connection;

    /**
     * Initializes the database connection.
     */
    public function __construct()
    {
        global $CONFIG;
        $this->connection = new \mysqli(
            $CONFIG['database']['host'],
            $CONFIG['database']['username'],
            $CONFIG['database']['password'],
            $CONFIG['database']['name']
        );
        if ($this->connection->connect_errno) {
            $this->handleError('Connection', $this->connection->connect_errno, $this->connection->connect_error);
        } else {
            $this->connection->set_charset('UTF-8');
        }
    }

    /**
     * Closes the database connection.
     */
    public function __destruct()
    {
        $this->connection->close();
    }

    /**
     * Executes a prepared MySQL query.
     * Based on https://stackoverflow.com/q/13387155
     * @param  string $querysql MySQL statement to execute
     *                          Mark spaces for variables like (?)
     * @param  string $types    Indicate the type of the following variables
     *                          Use 'i' for integers, 's' for strings, 'd' for doubles
     *                          E.g. your variables are two strings then an int 'ssi'
     * @param  mixed $variables Accepts a variable number of variables
     *                          Denote types using $types
     * @return array            Results from your query
     */
    public function query($querysql)
    {
        if ($query = $this->connection->prepare($querysql)) {
            if (func_num_args() > 1) {
                $x = func_get_args();
                $args = array_merge(
                    array(func_get_arg(1)),
                    array_slice($x, 2)
                );
                $args_ref = array();
                foreach ($args as $k => &$arg) {
                    $args_ref[$k] = &$arg;
                }
                if (!call_user_func_array(array($query, 'bind_param'), $args_ref)) {
                    $this->handleError('Binding parameters');
                }
            }
            if (!$query->execute()) {
                $this->handleError('Execution');
            }

            if ($query->affected_rows > -1) {
                return $query->affected_rows;
            }

            $params = array();
            $meta = $query->result_metadata();
            while ($field = $meta->fetch_field()) {
                $params[] = &$row[$field->name];
            }
            call_user_func_array(array($query, 'bind_result'), $params);

            $result = array();
            while ($query->fetch()) {
                $r = array();
                foreach ($row as $key => $val) {
                    $r[$key] = $val;
                }
                $result[] = $r;
            }

            $query->close();
            return $result;
        } else {
            $this->handleError('Preparing query');
        }
    }

    /**
     * Called to handle MySQL errors of all kinds. Obviously, this needs to be improved.
     * @param  string $action Describes what action was being attempted at time of failure.
     */
    private function handleError($action, $number = -1, $text = '')
    {
        if ($number === -1) {
            echo $action . ' failed: (' . $this->connection->errno . ') ' . $this->connection->error. '. Please use the Back button to return to the previous page and correct your input. If the problem persists, please contact the hostmaster.';
            exit;
        } else {
            echo $action . ' failed: (' . $number . ') ' . $text. '. Please use the Back button to return to the previous page and correct your input. If the problem persists, please contact the hostmaster.';
            exit;
        }
    }

    public function handle()
    {
        return $this->connection;
    }
}
