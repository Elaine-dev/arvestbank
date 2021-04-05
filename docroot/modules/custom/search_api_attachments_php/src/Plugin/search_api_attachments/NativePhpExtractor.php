<?php

namespace Drupal\search_api_attachments_php\Plugin\search_api_attachments;

use Drupal\Core\Form\FormStateInterface;
use Drupal\search_api_attachments\TextExtractorPluginBase;
use Drupal\file\Entity\File;
use Smalot\PdfParser\Parser;

/**
 * Provides native_php_extractor extractor.
 *
 * @SearchApiAttachmentsTextExtractor(
 *   id = "native_php_extractor",
 *   label = @Translation("Native PHP Extractor (smalot/pdfparser)"),
 *   description = @Translation("Adds native php extraction support."),
 * )
 */
class NativePhpExtractor extends TextExtractorPluginBase {

  /**
   * Extract file with Pdftotext command line tool.
   *
   * @param \Drupal\file\Entity\File $file
   *   A file object.
   *
   * @return string
   *   The text extracted from the file.
   */
  public function extract(File $file) {

    // If this is a pdf file.
    if (in_array($file->getMimeType(), $this->getPdfMimeTypes())) {

      // Load parser.
      $parser = new Parser();

      // Get file path.
      $uri = $file->getFileUri();
      $stream_wrapper_manager = \Drupal::service('stream_wrapper_manager')->getViaUri($uri);
      $file_path = $stream_wrapper_manager->realpath();

      // Load pdf into parser.
      $pdf = $parser->parseFile($file_path);

      // Get contents of pdf.
      $output = $pdf->getText();

      // Throw an error if we extracted no info from the pdf.
      if (is_null($output)) {
        throw new \Exception('Null output from NativePhpExtractor.');
      }

      // Return our output.
      return $output;
    }
    // If this is not a pdf file.
    else {
      return NULL;
    }

  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
  }

}
