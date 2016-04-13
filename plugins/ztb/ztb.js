CKFinder.define( function () {

     var MyPlugin = {
         init: function ( finder ) {
             //点击文件夹就展开             
             finder.on( 'folder:selected', function ( evt ) {
               $('.ui-btn-active').next().click()
             } );

             finder.on( 'folder:getFiles:after', function(evt){
                console.log('folder:getFiles:after======!!!')
                console.log(evt)
                var $descs = $('.ckf-file-desc p');
                console.log($descs.length)
                for(var i = 0; i<$descs.length;i++){
                  var e = $descs[i];
                  var outerHTML = e.outerHTML;
                  var innerHTML = e.innerHTML;
                  var sp = innerHTML.split('<br>');
                  e.innerHTML = '<span class="jy-date">' + sp[0] + '</span><span class="jy-size" style="float: right;">' + sp[1] + '</span>';
                }
             });
         }
     };

     return MyPlugin;
 } );
