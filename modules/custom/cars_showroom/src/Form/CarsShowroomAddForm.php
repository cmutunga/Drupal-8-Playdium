<?php

namespace Drupal\cars_showroom\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CarsShowroomAddForm extends FormBase {

    Protected $connection;

    public function __construct(Connection $connection) {

        $this->connection = $connection;
    }


    public static function create(ContainerInterface $container) {

            $connection = $container->get('database');

        return new static($connection );
    }

    public function getFormId() {
        return 'cars_showroom_add_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state) {

        $form ['car']['car_id'] = [
            '#type' => 'textfield',
            '#size' => 20,
            '#title' => $this->t('Car ID'),
        ];

        $form['car'] ['make'] = [
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

        $form ['car']['emp_id'] = [
            '#type' => 'textfield',
            '#size' => 20,
            '#title' => $this->t('EmpID'),
        ];

        $form ['car']['add']= [
            '#type' => 'submit',
            '#value' => $this->t('Add this car!'),
        ];

        return $form['car'];
    }

    public function submitForm(array &$form, FormStateInterface $form_state) {
        // grab element values and build them into array that represents a DB table record
        $record = [
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
         $this->connection->insert('car_inventory')->fields($record)->execute();

    }

}