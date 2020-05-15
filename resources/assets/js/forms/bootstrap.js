/**
 * Initialize the Spark form extension points.
 */
window.App.forms = {

};

/**
 * Load the SparkForm helper class.
 */
require('./form');

/**
 * Define the SparkFormError collection class.
 */
require('./errors');

/**
 * Add additional HTTP / form helpers to the Spark object.
 */
window.$.extend(window.App, require('./http'));
