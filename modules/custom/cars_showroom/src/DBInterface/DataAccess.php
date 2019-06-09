<?php

namespace drupal\cars_showroom\DBInterface;
use Drupal\Core\Database\Connection;

class DataAccess {

    Protected $connection;

    public function __construct(Connection $connection) {

        $this->connection = $connection;
    }

    public function getInventory(){
        /*to hold table rows*/
        $carInventory = [];

        /*to retrieve several records -- all cars in inventory  */
        $queryRtn = $this->connection->select('car_inventory')->fields('car_inventory');
        $selectCars = $queryRtn->execute()->fetchAll();
        $row = [];

        /*build our cars inventory table -- add each record to our table's row*/
        foreach ($selectCars as $car) {
            $key = strval($car->car_id);
            $row [$key] =  ['id' =>$car->car_id, 'mk' =>$car->make, 'md' =>$car->model, 'cl' => $car->color, 'od' =>$car->odo, 'yr' =>$car->year, 'lp' =>$car->list_price, 'emp' =>$car->emp_id,];
            array_push($carInventory,$row[$key]);
        }
        return $carInventory;
    }

    public function getEmployees(){
        /*to hold table rows*/
        $allEmployees = [];

        //to retrieve several records -- all employees
        $queryRtn = $this->connection->select('employee')->fields('employee');
        $selectEmps = $queryRtn->execute()->fetchAll();
        $row = [];

        //build our employee table -- add each record to our table's row
        foreach ($selectEmps as $emp) {
            $key = strval($emp->emp_id);
            $row [$key] =  ['id' =>$emp->emp_id, 'fn' =>$emp->first_name, 'ln' =>$emp->last_name, 'em' => $emp->email, 'isMnr' =>$emp->is_manager, 'mnrID' =>$emp->manager_id,];
            array_push($allEmployees,$row[$key]);
        }
        return $allEmployees;
    }

    public function getCustomers(){
        /*to hold table rows*/
        $allCustomers = [];
        //to retrieve several records -- all clients
        $queryRtn = $this->connection->select('customer')->fields('customer');
        $selectClients = $queryRtn->execute()->fetchAll();
        $row = [];

        foreach ($selectClients as $client) {
            $key = strval($client->client_id);
            $row [$key] =  ['id' =>$client->client_id, 'fn' =>$client->first_name, 'ln' =>$client->last_name, 'em' => $client->email, 'phone' =>$client->phone,];
            array_push($allCustomers,$row[$key]);
        }
        return $allCustomers;

    }



}
