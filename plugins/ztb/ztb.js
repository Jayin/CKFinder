CKFinder.define( function () {

     var MyPlugin = {
         init: function ( finder ) {
             //点击文件夹就展开             
             finder.on( 'folder:selected', function ( evt ,e) {
               $('.ui-btn-active').next().click()
             } );
             
         }
     };

     return MyPlugin;
 } );
