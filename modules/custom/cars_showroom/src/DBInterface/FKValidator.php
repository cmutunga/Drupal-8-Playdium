<?php

namespace drupal\cars_showroom\DBInterface;
use Drupal\Core\Database\Connection;

class FKValidator
{

    Protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getManagers () {

        //Drupal DB does not enforce or even implement FK constraints
        //this hack implements foreign key validation in front end code meaning that only
        // a valid manager can be entered in the employee's "manager_id" field
        //This is done by retrieving all valid managers and allowing
        // only them as input in the select button

        /*several records -- only managers in employee table  */
        $queryRtn = $this->connection->select('employee', 'x')
            ->fields('x',['emp_id','first_name','last_name','is_manager'])
            ->condition('x.is_manager','1','=');
        $managers = $queryRtn->execute()->fetchAll();

        /*build an array, mnrRows,
        to be used in the options property of the manager_id select element */
        $mnrRows = [];

        foreach ($managers as $boss) {
            $bossID = $boss->emp_id;
            $bossName = $boss->first_name.' '. $boss->last_name;
            $row = [strval($bossID) => strval($bossName)];
            array_push($mnrRows, $row);
        }
        return $mnrRows;

    }

    public function getSalesPersons(){
        //front end FK validation once more, only sales persons can be entered herer
        /*several records -- only managers in employee table  */
        $queryRtn = $this->connection->select('employee', 'x')
            ->fields('x',['emp_id','first_name','last_name','is_manager'])
            ->condition('x.is_manager','0','=');
        $salesPersons = $queryRtn->execute()->fetchAll();

        /*build an array, mnrRows,
        to be used in the options property of the manager_id select element */
        $sellersRows = [];

        foreach ($salesPersons as $seller) {
            $sellerID = $seller->emp_id;
            $sellerName = $seller->first_name.' '. $seller->last_name;
            $row = [strval($sellerID) => strval($sellerName)];
            array_push($sellersRows, $row);
        }
        return $sellersRows;

    }

}