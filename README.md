## Entity reference - customizability at its best

From
http://kybest.hu/en/blog/entity-reference-customizability-at-its-best

The Entity reference module is one of the most used Drupal modules. At the moment of writing it is 51st on the usage list of Drupal projects. Those who have already made a Drupal site probably have knows it. At the same time, not many know what a powerful tool it can be even in special cases.

The quality of a Drupal module is quite well characterized by its customizability. If a module produces markup for instance we expect to be able to alter that markup through the theme layer of Drupal. The Entity reference is an excellently written module in this aspect (too). But what should be customized in the Entity reference module?

In one of the sites recently produced by us one part of the content comes from an external source. These contents are digitalized books stored on the Drupal site as simple nodes with "book" content type. The books are not submitted via the Drupal "Create Content" form but instead an XML file with the book text is imported. However, the XML file is updated from time to time: when this happens we remove the book nodes from the Drupal site and reimport the XML file.

An eternal problem is the handling of the references pointing to reimported (or migrated) content. The Entity reference module stores the node nid as default and on reimport this nid changes. We could of course update the book nodes instead of deleting and reimporting them but we chose another way.

Each book content got a unique string id that came from the XML file and that we store in a (field_id) field on the Drupal level. This id is the id that does not change after reimporting. The problem to be solved was reduced to get the Entity reference module store this unique id instead of the nid. And that's where Entity reference selection and behavior plugins enter the picture.

These are ctools plugins serving the purpose of customizing every bit of the entityreference field to our liking. Selection plugins modify the behaviour of the widget used for the field. Behavior plugins regulate the access, loading, saving and storing of the field value and the views integration.

Let us see, how the implementation looks like! (Some custom functions are not attached, we only describe their functionality.)

As with every ctools plugin, first of all we need a hook_ctools_plugin_directory().
