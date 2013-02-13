/**
 * Provides API access to the Lemur backend.
 */
if (typeof lemur === 'undefined') var lemur = {};

lemur.api = (function ($) {
	var self = {};
	
	// Prefix for API requests
	self.prefix = '/lemur/api/';

	// Enable/disable debugging output to the console
	self.debug = false;
	
	// Helper function to verify parameters
	var _has = function (obj, prop) {
		return obj.hasOwnProperty (prop);
	};
	
	// Console log wrapper for debugging
	var _log = function (obj) {
		if (self.debug) {
			console.log (obj);
		}
		return obj;
	};
	
	// API call to /lemur/api/data/submit
	self.data_submit = function (data, callback) {
		if (! _has (data, 'id')) {
			throw new Error ('lemur.api.data_submit() - Missing parameter: id');
		}
		
		if (! _has (data, 'answer')) {
			throw new Error ('lemur.api.data_submit() - Missing parameter: answer');
		}
		
		$.post (
			_log (self.prefix + 'data/submit/' + encodeURIComponent (data.id)),
			_log ({ answer: data.answer }),
			callback
		);
		
		return self;
	};
	
	return self;
})(jQuery);