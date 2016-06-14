ZKUploader.define( {
    config: function( config ) {
        // Use sample custom theme.
        config.themeCSS = 'skins/ztb/zkuploader.css';

        // Use Moono skin icons.
        config.iconsCSS = 'skins/ztb/icons.css';

        return config;
    },

    init: function( finder ) {
        ZKUploader.require( [ 'jquery' ], function( jQuery ) {
            // Enforce black iconset.
            jQuery( 'body' ).addClass( 'ui-alt-icon' );
        } );
    }
} );
