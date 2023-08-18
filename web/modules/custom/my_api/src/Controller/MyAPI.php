<?php

namespace Drupal\my_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Getting the content data as jsondata.
 */
class MyAPI extends ControllerBase {
  protected $entityManager;

  /**
   * Instantiating EntityTypeManagerInterface $entity.
   */
  public function __construct(EntityTypeManagerInterface $entity) {
    $this->entityManager = $entity;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * Getting the Products nodes using Request module.
   */
  public function view(Request $req) {
    $headers = $req->headers->get('api');
    $content_types = $this->entityManager->getStorage('node_type')->loadMultiple();
    $data['content_type'] = [];
    foreach ($content_types as $content_type) {
      $nodes = $this->entityManager->getStorage('node')->loadByProperties([
        'type' => $content_type->getOriginalId(),
      ]);
      $data['content_type'][$content_type->getOriginalId()] = [];
      foreach ($nodes as $node) {
        $field_names = $node->getFieldDefinitions();
        foreach ($field_names as $field_name => $field_definition) {
          // Get the field value.
          $field_value = $node->get($field_name)->getValue();
          // Do something with the field name and value.
          foreach ($field_value as $item) {
            // The actual value is usually in the 'value' key.
            $value = $item['value'];
            // Do something with the field name, value, and langcode.
            $values[$field_name] = $value;
          }
        }
        $data['content_type'][$content_type->getOriginalId()][] = $values;
      }
    }

    $result = [];
    // For each product just getting the Title, description, price, images fields.
    foreach ($data['content_type']['product'] as $key => $product) {
      $result[] = [$product['title'], $product['field_description'], $product['field_price'], $product['image']];
    }

    return new JsonResponse($result);
  }

}
