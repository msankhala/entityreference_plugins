<?php
 
/**
 * A Book Entity reference selection handler.
 *
 * This handles the entityreference field widget.
 *
 * There are some more methods that can be implemented, e.g. in order
 * to use an autocomplete widget. For examples and further description see
 * entityreference/plugins/selection/ and the OG module.
 *
 */
class BookTextEntityReference_SelectionHandler extends EntityReference_SelectionHandler_Generic {
 
  /**
   * Overrides EntityReference_SelectionHandler_node::getInstance().
   *
   * This does not do much but is needed.
   */
  public static function getInstance($field, $instance = NULL, $entity_type = NULL, $entity = NULL) {
    return new BookTextEntityReference_SelectionHandler($field, $instance, $entity_type, $entity);
  }
 
  /**
   * Overrrides EntityReference_SelectionHandler_Generic::settingsForm().
   *
   * We don't want any settings on our settings form, not even the
   * default entity and bundle selection.
   */
  public static function settingsForm($field, $instance) {
    return array();
  }
 
  /**
   * Overrides EntityReference_SelectionHandler_Generic::getReferencableEntities().
   *
   * Get the options for our widget. The keys are the book node nids,
   * the values are the book titles.
   *
   * To keep it simple, the $match, $match_operator and $limit
   * arguments are not used.
   *
   * Normally, Entity reference uses an EFQ here but that can be very resource
   * intensive as to get the title each entity must be loaded. So we just use
   * a db_select() inside a custom mymodule_book_get_ref_titles() function.
   *
   */
  public function getReferencableEntities($match = NULL, $match_operator = 'CONTAINS', $limit = 0) {
    /// We store the book chapters in a 'book' content type.
    $bundles = array('book');
    $return = array();
    foreach ($bundles as $bundle) {
      // mymodule_book_get_ref_titles() just returns an array with all
      // referencable book node nids as keys and the node titles as values.
      $return[$bundle] =  mymodule_book_get_ref_titles($bundle);
    }
    return $return;
  }
 
  /**
   * Overrides EntityReference_SelectionHandler_Generic::countReferencableEntities().
   *
   * Surprise: it returns the number of referencable entities.
   */
  public function countReferencableEntities($match = NULL, $match_operator = 'CONTAINS') {
    // mymodule_book_get_ref_titles() just returns an array with all
    // referencable book node nids as keys and the node titles as values.
    $referencable_entities = mymodule_book_get_ref_titles();
    return count($referencable_entities);
  }
 
  /**
   * Overrides EntityReference_SelectionHandler_Generic::validateReferencableEntities().
   *
   * This method is called from entityreference_field_validate, which
   * is a hook_field_validate() implementation. This method must
   * return the accepted entity ids.
   *
   * validateReferencableEntities() might be different from
   * getReferencableEntities() in that the latter does not necessarily
   * return the entity ids (though in our case it does.)
   */
  public function validateReferencableEntities(array $ids) {
    if ($ids) {
      // mymodule_book_get_ref_titles() just returns an array with all
      // referencable book node nids as keys and the node titles as values.
      $referencable_entities = mymodule_book_get_ref_titles(array('book'));
      return array_keys($referencable_entities);
    }
    return array();
  }
 
  /**
   * Overrides EntityReference_SelectionHandler_Generic::getLabel().
   *
   * When the widget displays the selected entities this method is called.
   * We display the field_title field of the node instead of its title
   * property.
   */
  public function getLabel($entity) {
    $label = field_get_items('node', $entity, 'field_title');
    if ($label) {
      return $label[0]['value'];
    }
    else return '';
  }
 
}

