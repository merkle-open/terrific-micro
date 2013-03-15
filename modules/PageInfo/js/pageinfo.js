(function($) {
    /**
     * PageInfo module implementation.
     *
     * @author Roger Dudler <roger.dudler@namics.com>
     * @namespace Tc.Module
     * @class PageInfo
     * @extends Tc.Module
     */
    Tc.Module.PageInfo = Tc.Module.extend({
        
        on: function(callback) {
            var that = this;
            var $ctx = this.$ctx;

            console.log('PageInfo');

            callback();
        },
        
        after: function() {
            
        }

    });
})(Tc.$);