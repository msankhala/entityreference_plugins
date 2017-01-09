<?php
 
/**
 * @file
 * Entityreference plugin class for the Book reference behavior.
 */
 
/**
 * An entityreference field behavior plugin to handle the id strings.
 *
 * We extend EntityReference_BehaviorHandler_Abstract. For further
 * info on the methods see entityreference/plugins/behavior/abstract.inc.
 */
class EntityReferenceBehavior_BookRef extends EntityReference_BehaviorHandler_Abstract {
 
  /**
   * Implements EntityReference_BehaviorHandler_Abstract::schema_alter().
   *
   * This method gets called from entityreference_field_schema() which
   * is a hook_field_schema() implementation.
   *
   * First of all we need to modify the default entityreference field schema
   * that accepts only integer values to prepare it for our varchar ids.
   */
  public function schema_alter(&$schema, $field) {
    $schema['columns']['target_id']['type'] = 'varchar';
    $schema['columns']['target_id']['length'] = 255;
    $schema['columns']['target_id']['default'] = '';
    // varchar cannot be unsigned so we unset this.
    unset($schema['columns']['target_id']['unsigned']);
  }
 
  /**
   * Implements EntityReference_BehaviorHandler_Abstract::insert().
   *
   * This method gets called from entityreference_field_insert() which
   * is a hook_field_insert() implementation.
   *
   * We want to store the string id in the database, so we convert
   * the nid into it when inserting a new field value.
   */
  public function insert($entity_type, $entity, $field, $instance, $langcode, &$items) {
    $this->mymodule_transform_items($items);
  }
 
  /**
   * Implements EntityReference_BehaviorHandler_Abstract::update().
   *
   * This is the same as the previous method only that this gets called on
   * field update.
   */
  public function update($entity_type, $entity, $field, $instance, $langcode, &$items) {
    $this->mymodule_transform_items($items);
  }
 
  /**
   * Implements EntityReference_BehaviorHandler_Abstract::load().
   *
   * This method gets called from entityreference_field_load() which
   * is a hook_field_load() implementation.
   *
   * This method runs when a field is loaded (and is not fetched from
   * the cache). So this is the time to turn our custom string book
   * id into nid.
   */
  public function load($entity_type, $entities, $field, $instances, $langcode, &$items) {
    foreach ($entities as $entity) {
      $ids = field_get_items('node', $entity, $field['field_name']);
      if ($ids) {
        foreach ($ids as $id) {
          // You won't find mymodule_get_nid_by_id() in this article but
          // believe me: it simply converts a book string id into nid.
          $items[$entity->nid][]['target_id'] = mymodule_get_nid_by_id($id);
        }
      }
    }
  }
 
  /**
   * Helper function: Transform field items from nid to field_id values.
   */
  protected function mymodule_transform_items(&$items) {
    foreach ($items as $key => &$item) {
      // You won't find mymodule_get_id_by_nid() in this article but
      // believe me: it simply converts a book string nid into id.
      $item['target_id'] = mymodule_get_id_by_nid($item['target_id']);
    }
  }
 
  /**
   * Implements EntityReference_BehaviorHandler_Abstract::views_data_alter().
   *
   * This method gets called from entityreference_field_views_data() which
   * is a hook_field_views_data() implementation.
   *
   * To use views relationships in the usual way we need to use a custom
   * relationship handler (mymodule_views_handler_relationship_book_ref)
   * that joins the node, the book id and the entityreference field tables.
   * This views relationship handler is out of the scope of this article.
   *
   * For the reverse relationship a separate hook_views_data_alter()
   * implementation is needed.
   */
  public function views_data_alter(&$data, $field) {
    // We need to join in the field_data_field_id table in the middle.
    $data['field_data_field_text_refs']['field_text_refs_target_id']['relationship']['middle_table'] = 'field_data_field_id';
    $data['field_data_field_text_refs']['field_text_refs_target_id']['relationship']['middle_table_left_field'] = 'field_id_value';
    $data['field_data_field_text_refs']['field_text_refs_target_id']['relationship']['middle_table_right_field'] = 'entity_id';
    $data['field_data_field_text_refs']['field_text_refs_target_id']['relationship']['handler'] = 'mymodule_views_handler_relationship_book_ref';
 
    // To make the story complete we should alter the revision relationships too
    // but since we don't need them we won't.
  }
}

