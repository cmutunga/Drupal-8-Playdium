<?php

namespace Drupal\cars_showroom\Form;

use drupal\cars_showroom\DBInterface\DataAccess;
use drupal\cars_showroom\DBInterface\FKValidator;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CarsForm extends FormBase{

    Protected $dbAccess;
    Protected $fkValidator;

    public function __construct(DataAccess $dbAccess, FKValidator $fkValidator) {
        $this->dbAccess = $dbAccess;
        $this->fkValidator = $fkValidator;
    }

    public static function create(ContainerInterface $container) {
        $dbAccess = $container->get('db_access');
        $fkValidator = $container->get('fk_validator');
        return new static($dbAccess, $fkValidator );
    }

    public function getFormId() {
        return 'cars_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state) {

                 $form ['data_selector'] = [
                    '#type' => 'select',
                    '#title' => $this->t('Type of entry'),
                    '#options' => [
                        '1' => $this->t('List or Delete Cars'),
                        '2' => $this->t('Add Cars'),
                        '3' => $this->t('Edit Cars'),
                        ],
                 ];



                //1. Build & return list of cars
                // 1.1 start with table headers
                $carHeaders = [
                    'id' => $this->t('ID'),
                    'mk' => $this->t('Make'),
                    'md' => $this->t('Model'),
                    'cl' => $this->t('Color'),
                    'od' => $this->t('Odo'),
                    'yr' => $this->t('Year'),
                    'lp' => $this->t('List'),
                    'emp' => $this->t('EmpID'),
                ];

                //1.2 Call DB access service to return an inventory array
                $carInventory = $this->dbAccess->getInventory();
                //1.3 use inventory array and headers to build render array as table on a form
                $car['list']  = [
                    '#type' => 'tableselect',
                    '#caption' => $this
                        ->t('Cars For Sale'),
                    '#header' => $carHeaders,
                    '#options' => $carInventory,
                ];



                $car ['add']['car_id'] = [
                    '#type' => 'textfield',
                    '#size' => 20,
                    '#title' => $this->t('Car ID'),
                ];

                $car ['add']['make'] = [
                    '#type' => 'textfield',
                    '#size' => 20,
                    '#title' => $this->t('Car Make'),
                ];

                $car ['add']['model'] = [
                    '#type' => 'textfield',
                    '#size' => 20,
                    '#title' => $this->t('Model'),
                ];

                $car ['add']['color'] = [
                    '#type' => 'textfield',
                    '#size' => 20,
                    '#title' => $this->t('Color'),
                ];

                $car ['add']['odo'] = [
                    '#type' => 'textfield',
                    '#size' => 20,
                    '#title' => $this->t('Odo Reading'),
                ];

                $car ['add']['year'] = [
                    '#type' => 'textfield',
                    '#size' => 20,
                    '#title' => $this->t('Year'),
                ];

                $car ['add']['list_price'] = [
                    '#type' => 'textfield',
                    '#size' => 20,
                    '#title' => $this->t('List Price'),
                ];

                //FK validation -- ensures only bonafide sales persons can be entered in this field
                $salesPersons = $this->fkValidator->getSalesPersons();
                $car ['add']['emp_id'] = [
                    '#type' => 'select',
                    '#title' => $this->t('Sales Person'),
                    '#options' => $salesPersons,
                ];

                $form = $car['add'];

                /*
                $form ['action']= [
                    '#type' => 'submit',
                    '#value' => $this->t('Add this!'),
                ];
                */

                $form['#cache']['max-age'] = 0;
                return $form;
    }

    public function submitForm(array &$form, FormStateInterface $form_state){

        // grab car element values and build them into array that represents a DB table record
        $carData = [
            'car_id' => $form_state->getValue('car_id'),
            'make' => $form_state->getValue('make'),
            'model' => $form_state->getValue('model'),
            'color' => $form_state->getValue('color'),
            'odo' => $form_state->getValue('odo'),
            'year' => $form_state->getValue('year'),
            'list_price' => $form_state->getValue('list_price'),
            'emp_id' => $form_state->getValue('emp_id'),
        ];
        //insert record into DB table
         //$this->connection->insert('car_inventory')->fields($carData)->execute();
        $targetTable = 'car_inventory';
        $this->dbAccess->insertNewRecord($carData, $targetTable);
    }

    public function validateForm(array &$form, FormStateInterface $form_state){

    }


}
