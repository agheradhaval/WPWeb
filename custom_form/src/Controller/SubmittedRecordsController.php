<?php
namespace Drupal\custom_form\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\media\Entity\Media;
use Drupal\file\Entity\File;



/**
 * Controller for displaying submitted records.
 */
class SubmittedRecordsController extends ControllerBase {
  
  protected $database;

  public function __construct(Connection $database) {
    $this->database = $database;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database')
    );
  }

  public function content() {
  
    $query = $this->database->select('custom_form_data', 'cfd')
      ->fields('cfd')
      ->extend('Drupal\Core\Database\Query\PagerSelectExtender')
      ->extend('Drupal\Core\Database\Query\TableSortExtender');
    $results = $query->execute()->fetchAll();
    $rows = [];
    foreach ($results as $row) {

 
      $file = \Drupal\file\Entity\File::load($row->photo);
      $absolute_file_url = $file->createFileUrl(FALSE);

      $rows[] = [
          $row->id,
          $row->name,
          $row->runame,
          $row->date_of_birth,
          $row->gender,
          $absolute_file_url,
      ];
    }

    return array (
      '#theme' => 'submitted_records',
      '#data' => $rows,
      '#attached' => [
        'library' => ['custom_form/custom-table-styles'],
      ],
      '#cache' => ['max-age' => 0],
    );
  }
}
