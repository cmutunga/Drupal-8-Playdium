<?php

namespace Drupal\cars_showroom\Form;

use Drupal\Core\Database\Connection;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use drupal\cars_showroom\DBInterface\DataAccess;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ListDeleteRecordsForm extends FormBase {

    Protected $connection;
    Protected $dbAccess;

    public function __construct(Connection $connection, DataAccess $dbAccess) {

        $this->connection = $connection;
        $this->dbAccess = $dbAccess;

    }

    public static function create(ContainerInterface $container) {

        $connection = $container->get('database');
        $dbAccess = $container->get('db_access');

        return new static($connection, $dbAccess );
    }

    public function getFormId() {
        return 'list_delete_records_form';
    }


    public function buildForm(array $form, FormStateInterface $form_state) {

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
        $cars  = [
            '#type' => 'tableselect',
            '#caption' => $this
                ->t('Cars For Sale'),
            '#header' => $carHeaders,
            '#options' => $carInventory,
        ];


        //2. Build list of employees
        //2.1 start with table headers
        $employeeHeaders = [
            'id' => $this->t('ID'),
            'fn' => $this->t('First Name'),
            'ln' => $this->t('LastName'),
            'em' => $this->t('Email'),
            'isMnr' => $this->t('Is Manager?'),
            'mnrID' => $this->t('Manager ID'),
        ];
        //2.2 Call DB access service to return an employee array
        $allEmployees = $this->dbAccess->getEmployees();
        //2.3 use employee array and headers to build render array as table on a form
        $employees = [
            '#type' => 'tableselect',
            '#caption' => $this
                ->t('All Employees'),
            '#header' => $employeeHeaders,
            '#options' => $allEmployees,
        ];


        //3. Build list of customers
        //3.1 start with table headers
        $customerHeaders = [
            'id' => $this->t('ID'),
            'fn' => $this->t('First Name'),
            'ln' => $this->t('LastName'),
            'em' => $this->t('Email'),
            'phone' => $this->t('Phone'),
        ];
        //3.2 Call DB access service to return a customer array
        $allCustomers = $this->dbAccess->getCustomers();
        //2.3 use customer array and headers to build render array as table on a form
        $form ['customers'] = [
            '#type' => 'tableselect',
            '#caption' => $this
                ->t('All Employees'),
            '#header' => $customerHeaders,
            '#options' => $allCustomers,
        ];



        //4. Select which list to show

        /*
        $form['data_selector'] = [
            '#type' => 'select',
            '#title' => $this->t('Select Data'),
            '#options' => [
                '1' => $this->t('Employees'),
                '2' => $this->t('Inventory'),
                '3' => $this->t('Customers'),
            ],
        ];


        $form ['remove']= [
            '#type' => 'submit',
            '#value' => $this->t('Delete Selected.'),
        ];

       /*

        $form ['remove']= [
            '#type' => 'submit',
            '#value' => $this->t('Delete Selected.'),
         ];
        */


        //5. Show selected list
        $form['#cache']['max-age'] = 0;
        return $form ;

    }

    public function submitForm(array &$form, FormStateInterface $form_state){


    }

    public function validateForm(array &$form, FormStateInterface $form_state){

    }

}
