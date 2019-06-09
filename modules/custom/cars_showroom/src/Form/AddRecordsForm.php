<?php
namespace Drupal\cars_showroom\Form;

use Drupal\Core\Database\Connection;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AddRecordsForm extends FormBase {

    Protected $connection;

    public function __construct(Connection $connection) {

        $this->connection = $connection;
    }


    public static function create(ContainerInterface $container) {

        $connection = $container->get('database');

        return new static($connection );
    }

    public function getFormId() {
        return 'add_records_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state) {



    }

    public function submitForm(array &$form, FormStateInterface $form_state){


    }

    public function validateForm(array &$form, FormStateInterface $form_state){

    }


}
