<?php

namespace Drupal\arvestbank_migrate\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\media\Entity\Media;
use Drupal\media_acquiadam\AssetData;

/**
 * Class TestController.
 */
class TestController extends ControllerBase {

  /**
   * Render.
   *
   * @return string
   *   Return Hello string.
   */
  public function render() {

    $database = \Drupal::database();

    $mid = 28797;
    $media = Media::load($mid);
    dump($media);

    die();

    $input = 340;

    $queryString = "
      SELECT d.field_acquiadam_asset_id_value FROM media_field_data m
      INNER JOIN media_revision__field_acquiadam_asset_id d
      ON (m.vid = d.revision_id)
      WHERE m.name LIKE '%" . $input . "%'
      and m.bundle = 'acquia_dam_image'
    ";

    $query = $database->query($queryString);
    $result = $query->fetchAll();

    dump($result[0]->field_acquiadam_asset_id_value);

    die();
    $asset = new AssetData(\Drupal\Core\Database\Database::getConnection());

    $hey = $asset->get(121257135, NULL);

    dump($hey);

    die();

    $media = Media::load(28772);
    $media->save();


    return [
      '#type' => 'markup',
      '#markup' => $this->t('Implement method: render')
    ];
  }

}
