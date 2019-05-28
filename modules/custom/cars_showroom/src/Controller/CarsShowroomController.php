<?php

namespace drupal\cars_showroom\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CarsShowroomController extends ControllerBase {

    Protected $connection;

    public function __construct(Connection $connection) {

        $this->connection = $connection;
    }

    //instance new controller object and use dependency injection for database service
    public static function create(ContainerInterface $container) {

        $connection = $container->get('database');

        return new static($connection );
    }

    public function ourList()
    {
        $content = [];

        //start a table
        $car_headers = [
            $this->t('ID'),
            $this->t('Make'),
            $this->t('Model'),
            $this->t('Color'),
            $this->t('Odo'),
            $this->t('Year'),
            $this->t('List'),
            $this->t('EmpID'),
        ];

        //to hold table rows
        $carRows = [];

        /*several records -- all cars in inventory  */
        $queryRtn = $this->connection->select('car_inventory')->fields('car_inventory');
        $selectCars = $queryRtn->execute()->fetchAll();

        /*build our table -- add each record to our table's row*/
        foreach ($selectCars as $car) {
            // Sanitize each entry.
            $row = [$car->car_id, $car->make, $car->model, $car->color, $car->odo, $car->year, $car->list_price, $car->emp_id];
            array_push($carRows,$row);

            //below works too
            //$carRows[] = array_map('Drupal\Component\Utility\Html::escape', $row);
         }

        //render array
        $content['cars'] = [
            '#type' => 'table',
            '#caption' => $this->t('Cars Inventory.'),
            '#header' => $car_headers,
            '#rows' => $carRows,
            '#empty' => $this->t('No entries available.'),
        ];

        //dont cache this page content
        $content['#cache']['max-age'] = 0;

        return $content;
    }

}