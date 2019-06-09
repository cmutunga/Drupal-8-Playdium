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
            'id' => $this->t('ID'),
            'mk' => $this->t('Make'),
            'md' => $this->t('Model'),
            'cl' => $this->t('Color'),
            'od' => $this->t('Odo'),
            'yr' => $this->t('Year'),
            'lp' => $this->t('List'),
            'emp' => $this->t('EmpID'),
        ];

        //to hold table rows
        $carRows = [];


        /*several records -- all cars in inventory  */
        $queryRtn = $this->connection->select('car_inventory')->fields('car_inventory');
        $selectCars = $queryRtn->execute()->fetchAll();
        $row = [];
        /*build our table -- add each record to our table's row*/
        foreach ($selectCars as $car) {

            $key = strval($car->car_id);

            $row [$key] =  ['id' =>$car->car_id, 'mk' =>$car->make, 'md' =>$car->model, 'cl' => $car->color, 'od' =>$car->odo, 'yr' =>$car->year, 'lp' =>$car->list_price, 'emp' =>$car->emp_id,];

            array_push($carRows,$row[$key]);

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