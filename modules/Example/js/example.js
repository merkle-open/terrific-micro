(function($) {
    /**
     * Example module implementation.
     *
     * @author Roger Dudler <roger.dudler@namics.com>
     * @namespace Tc.Module
     * @class Example
     * @extends Tc.Module
     */
    Tc.Module.Example = Tc.Module.extend({
        
        on: function(callback) {
            var that = this;
            var $ctx = this.$ctx;

            console.log('Example');

            callback();
        },
        
        after: function() {
            
        }

    });
})(Tc.$);