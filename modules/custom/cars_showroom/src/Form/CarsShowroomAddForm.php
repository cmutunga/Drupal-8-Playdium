<?php

namespace Drupal\cars_showroom\Form;

use Drupal\Core\Database\Connection;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use drupal\cars_showroom\DBInterface\FKValidator;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CarsShowroomAddForm extends FormBase {

    Protected $connection;
    Protected $fkValidator;

    public function __construct(Connection $connection, FKValidator $fkValidator) {

        $this->connection = $connection;
        $this->fkValidator = $fkValidator;

    }


    public static function create(ContainerInterface $container) {

            $connection = $container->get('database');
            $fkValidator = $container->get('fk_validator');

        return new static($connection, $fkValidator );
    }

    public function getFormId() {
        return 'cars_showroom_add_form';
    }


    public function buildForm(array $form, FormStateInterface $form_state) {

    /*
        $form ['data_selector'] = [
            '#type' => 'select',
            '#title' => $this->t('Type of entry'),
            '#options' => [
                '1' => $this->t('Cars'),
                '2' => $this->t('Employees'),
                '3' => $this->t('Customers'),
            ],
        ];
    */
        $form ['car']['car_id'] = [
            '#type' => 'textfield',
            '#size' => 20,
            '#title' => $this->t('Car ID'),
        ];

        $form ['car']['make'] = [
            '#type' => 'textfield',
            '#size' => 20,
            '#title' => $this->t('Car Make'),
        ];

        $form ['car']['model'] = [
            '#type' => 'textfield',
            '#size' => 20,
            '#title' => $this->t('Model'),
        ];

        $form ['car']['color'] = [
            '#type' => 'textfield',
            '#size' => 20,
            '#title' => $this->t('Color'),
        ];

        $form ['car']['odo'] = [
            '#type' => 'textfield',
            '#size' => 20,
            '#title' => $this->t('Odo Reading'),
        ];

        $form ['car']['year'] = [
            '#type' => 'textfield',
            '#size' => 20,
            '#title' => $this->t('Year'),
        ];

        $form ['car']['list_price'] = [
            '#type' => 'textfield',
            '#size' => 20,
            '#title' => $this->t('List Price'),
        ];

       //FK validation -- ensures only bonafide sales persons can be entered in this field
        $salesPersons = $this->fkValidator->getSalesPersons();
        $form ['car']['emp_id'] = [
            '#type' => 'select',
            '#title' => $this->t('Sales Person'),
            '#options' => $salesPersons,
        ];

        $form ['car']['add']= [
            '#type' => 'submit',
            '#value' => $this->t('Add this!'),
        ];


        $form ['customer']['client_id'] = [
            '#type' => 'textfield',
            '#size' => 20,
            '#title' => $this->t('Client ID'),
        ];

        $form ['customer']['first_name'] = [
            '#type' => 'textfield',
            '#size' => 20,
            '#title' => $this->t('First Name'),
        ];

        $form ['customer']['last_name'] = [
            '#type' => 'textfield',
            '#size' => 20,
            '#title' => $this->t('Last Name'),
        ];

        $form ['customer']['email'] = [
            '#type' => 'textfield',
            '#size' => 20,
            '#title' => $this->t('email'),
        ];

        $form ['customer']['phone'] = [
            '#type' => 'textfield',
            '#size' => 20,
            '#title' => $this->t('phone number'),
        ];

        $form ['customer']['add']= [
            '#type' => 'submit',
            '#value' => $this->t('Add this!'),
        ];



        $form ['employee']['emp_id'] = [
            '#type' => 'textfield',
            '#size' => 20,
            '#title' => $this->t('Employee ID'),
        ];

        $form ['employee']['first_name'] = [
            '#type' => 'textfield',
            '#size' => 20,
            '#title' => $this->t('First Name'),
        ];

        $form ['employee']['last_name'] = [
            '#type' => 'textfield',
            '#size' => 20,
            '#title' => $this->t('Last Name'),
        ];

        $form ['employee']['email'] = [
            '#type' => 'textfield',
            '#size' => 20,
            '#title' => $this->t('email'),
        ];

        $form ['employee']['is_mnr'] = [
            '#type' => 'select',
            '#title' => $this->t('Is Manager?'),
            '#options' => [
                '0' => $this->t('No'),
                '1' => $this->t('Yes'),
            ],
        ];

        //FK validation --- ensure only bonafide managers can be entered in this field
        $managers = $this->fkValidator->getManagers();

        $form ['employee']['mnr_id'] = [
            '#type' => 'select',
            '#title' => $this->t('Manager'),
            '#options' => $managers,
        ];

        $form ['employee']['add']= [
            '#type' => 'submit',
            '#value' => $this->t('Add this!'),
        ];

        $form['#cache']['max-age'] = 0;
        return $form ['car'];
    }

    public function submitForm(array &$form, FormStateInterface $form_state) {
        /*// grab car element values and build them into array that represents a DB table record
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
         $this->connection->insert('car_inventory')->fields($carData)->execute();


        $clientData = [
            'client_id' => $form_state->getValue('client_id'),
            'first_name' => $form_state->getValue('first_name'),
            'last_name' => $form_state->getValue('last_name'),
            'email' => $form_state->getValue('email'),
            'phone' => $form_state->getValue('phone'),
        ];
        //insert record into DB table
        $this->connection->insert('customer')->fields($clientData)->execute();
        */




        $empData = [
            'emp_id' => $form_state->getValue('emp_id'),
            'first_name' => $form_state->getValue('first_name'),
            'last_name' => $form_state->getValue('last_name'),
            'email' => $form_state->getValue('email'),
            'is_manager' => $form_state->getValue('is_mnr'),
            'manager_id' => $form_state->getValue('mnr_id'),
        ];
        //insert record into DB table
        $this->connection->insert('employee')->fields($empData)->execute();

    }

}